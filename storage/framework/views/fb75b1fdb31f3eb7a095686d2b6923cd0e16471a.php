
<?php $__env->startSection('title', 'My Dashboard'); ?>
<?php $__env->startSection('content'); ?>
    <?php if(Auth::user()->role == 'Sales'): ?>
        <?php if(Auth::user()->id == 16 || Auth::user()->id == 23): ?>
            <div class="row gy-4 mb-4">
                <!-- Congratulations card -->
                <div class="col-md-3 col-12">
                    <div class="card h-100">
                        <div class="card-body text-nowrap">
                            <h4 class="card-title mb-1 d-flex gap-2 flex-wrap">
                                Rise to the Top!
                                <strong><?php echo e(Auth::user()->name); ?></strong>🎉
                            </h4>
                            <p class="pb-0">Keep closing and lead the leaderboard.</p>
                            <h4 class="text-primary mb-1">Rp. <?php echo e($formattedTotalPrice); ?></h4>
                            <?php
                                $jumlah_target = 0;
                                $jumlah_target = ($target?->total > 0) ? ($poTotalPrice / $target->total) * 100 : 0;
                                $formatted_jumlah_target = number_format($jumlah_target, 3);
                            ?>
                            <p class="mb-2 pb-1"><?php echo e($formatted_jumlah_target); ?>% of target 🚀</p>
                            <a href="javascript:;" class="btn btn-sm btn-primary waves-effect waves-light">View Sales</a>
                        </div>
                        <img src="<?php echo e(asset('assets')); ?>/img/illustrations/trophy.png"
                            class="position-absolute bottom-0 end-0 me-3" height="140" alt="view sales">
                    </div>
                </div>
                <!--/ Congratulations card -->
                <div class="col-md-9 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Key Performance Indicator</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 col-12">
                                    <div class="row mb-3">
                                        <div class="col-4" style="padding-right: 0;">
                                            <div class="card bg-primary text-white w-100 cursor-pointer"
                                                data-bs-toggle="modal" data-bs-target="#newProduct">
                                                <h5 class="card-title text-white text-center my-4">
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
                                            <div class="card bg-warning text-white w-100 cursor-pointer"
                                                data-bs-toggle="modal" data-bs-target="#accurData">
                                                <h5 class="card-title text-white text-center my-4">
                                                    <i
                                                        class="menu-icon tf-icons mdi mdi-package-variant-closed-check m-0 fs-1"></i>
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
                                            <div class="card border-warning bg-warning border-1 w-100 h-100"
                                                data-bs-toggle="modal" data-bs-target="#delivery">
                                                <h5 class="card-title text-center text-white my-4">
                                                    <i
                                                        class="menu-icon tf-icons mdi mdi-truck-delivery-outline m-0 fs-1"></i>
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
                                            <div class="card bg-warning text-white w-100 cursor-pointer"
                                                data-bs-toggle="modal" data-bs-target="#response">
                                                <h5 class="card-title text-white text-center my-4">
                                                    <i
                                                        class="menu-icon tf-icons mdi mdi-account-heart-outline m-0 fs-1"></i>
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
                                            <div class="card bg-warning text-white w-100 cursor-pointer"
                                                data-bs-toggle="modal" data-bs-target="#rating">
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
                                            <div class="card bg-warning border-warning text-white border-1 w-100 h-100 cursor-pointer"
                                                data-bs-toggle="modal" data-bs-target="#customer">
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
                                                            $jumlahCustomer = 0;
                                                        ?>
                                                        <?php $__currentLoopData = $customerCount; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php
                                                                $jumlahCustomer += $item->average;
                                                            ?>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        <?php
                                                            $jumlahCustomer / $dataCustomer;
                                                            $persenCustomer = ($jumlahCustomer / 5) * 100;
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
                                            <div class="card bg-primary text-white w-100 cursor-pointer"
                                                data-bs-toggle="modal" data-bs-target="#SWin">
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
                                                    <?php echo e(@$persenSW ?? 0); ?> / <?php echo e(Auth::user()->id == 16 ? '120' : '60'); ?>

                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-4" style="padding-right: 0;">
                                            <div class="card bg-primary text-white w-100 cursor-pointer"
                                                data-bs-toggle="modal" data-bs-target="#video">
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
                                                    <i class="menu-icon tf-icons mdi mdi-cart-plus  m-0 fs-1"></i>
                                                </h5>
                                            </div>
                                        </div>

                                        <div class="col-8 d-flex flex-column justify-content-between">
                                            <div class="card border-success bg-label-success border-1 shadow-none">
                                                <h5 class="card-title text-center my-2">
                                                    Purchase Order
                                                </h5>
                                            </div>
                                            <div class="card border-success bg-label-success border-2 shadow-none mt-auto"
                                                style="border-style: dashed;">
                                                <h5 class="card-title text-center my-2">
                                                    <?php echo e($POCount); ?>

                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                $salesID = Auth::user()->id;
            ?>
            <?php echo $__env->make('components.modal.onlineSales.new-product', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php echo $__env->make('components.modal.onlineSales.akurasi-data', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php echo $__env->make('components.modal.onlineSales.delivery', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php echo $__env->make('components.modal.onlineSales.response', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php echo $__env->make('components.modal.onlineSales.rating', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php echo $__env->make('components.modal.onlineSales.customer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php echo $__env->make('components.modal.onlineSales.sw', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php echo $__env->make('components.modal.onlineSales.video', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?>
        <div class="row gy-4 mb-4">
            <!-- Congratulations card -->
            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                <div class="card clean-card h-100">
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
            <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12">
                <div class="card h-100">
                    <div class="card-body">
                        <?php
                            $jumlah_target = ($target?->total > 0) ? ($poTotalPrice / $target->total) * 100 : 0;
                            $formatted_jumlah_target = number_format($jumlah_target, 1);
                            $achColor = $jumlah_target >= 100 ? 'success' : ($jumlah_target >= 70 ? 'warning' : 'danger');
                        ?>
                        <div class="row align-items-center gy-3">
                            <div class="col-md-7">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge bg-label-primary">
                                        <i class="mdi mdi-trophy-outline"></i> Pencapaian Bulan Ini
                                    </span>
                                    <small class="text-muted"><?php echo e(now()->locale('id')->translatedFormat('F Y')); ?></small>
                                </div>
                                <h4 class="card-title mb-1 d-flex gap-2 flex-wrap">
                                    Rise to the Top! <strong><?php echo e(Auth::user()->name); ?></strong>🎉
                                </h4>
                                <p class="text-muted mb-2">Keep closing and lead the leaderboard.</p>
                                <h3 class="text-primary fw-bold mb-0">Rp <?php echo e($formattedTotalPrice); ?></h3>
                                <small class="text-muted">Target: Rp <?php echo e(number_format($target?->total ?? 0, 0, ',', '.')); ?></small>
                                <div class="mt-3">
                                    <a href="https://reftech.my.id/reports"
                                        class="btn btn-sm btn-primary waves-effect waves-light">
                                        <i class="mdi mdi-chart-areaspline me-1"></i> View Sales
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-5 text-center">
                                <div id="salesAchievementGauge" data-value="<?php echo e($jumlah_target); ?>"
                                    data-color="<?php echo e($achColor); ?>"></div>
                            </div>
                        </div>
                    </div>

                    <?php if(Auth::user()->id != 16 || Auth::user()->id != 23): ?>
                        <hr class="m-0">
                        <div class="card-body">
                            <h6 class="text-uppercase text-muted mb-3" style="letter-spacing:.5px;font-size:.75rem;">
                                <i class="mdi mdi-speedometer me-1"></i> Key Performance Indicator
                            </h6>
                            <?php
                                $kpiPct = function ($value, $target) {
                                    $pct = $target > 0 ? round(($value / $target) * 100, 1) : 0;
                                    $color = $pct >= 100 ? 'success' : ($pct >= 70 ? 'warning' : 'danger');
                                    return [$pct, $color];
                                };
                            ?>
                            <div class="row g-4">
                                <?php if(Auth::user()->id != '4'): ?>
                                    <?php
                                        [$pctLeads, $colorLeads] = $kpiPct($leads->count(), $target?->leads ?? 0);
                                    ?>
                                    <div class="col-6 col-md-4 col-xl">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="avatar avatar-sm">
                                                <div class="avatar-initial bg-label-secondary rounded">
                                                    <i class="mdi mdi-account-multiple-plus-outline"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?php echo e($leads->count()); ?>

                                                    <small class="text-muted fs-tiny">/ <?php echo e($target?->leads ?? 0); ?></small>
                                                </h6>
                                                <small class="text-muted">New Leads</small>
                                            </div>
                                        </div>
                                        <div class="progress" style="height:4px;border-radius:4px;">
                                            <div class="progress-bar bg-<?php echo e($colorLeads); ?>"
                                                style="width:<?php echo e(min($pctLeads, 100)); ?>%;border-radius:4px;"></div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php
                                    [$pctDc, $colorDc] = $kpiPct($dailyCall, $target?->dc ?? 0);
                                ?>
                                <div class="col-6 col-md-4 col-xl">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <div class="avatar avatar-sm">
                                            <div class="avatar-initial bg-label-info rounded">
                                                <i class="mdi mdi-phone-outline"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0"><?php echo e($dailyCall); ?>

                                                <?php if(Auth::user()->id != 3 && Auth::user()->id != 4): ?>
                                                    <small class="text-muted fs-tiny">/ <?php echo e($target?->dc ?? 0); ?></small>
                                                <?php endif; ?>
                                            </h6>
                                            <small class="text-muted">Daily Call</small>
                                        </div>
                                    </div>
                                    <?php if(Auth::user()->id != 3 && Auth::user()->id != 4): ?>
                                        <div class="progress" style="height:4px;border-radius:4px;">
                                            <div class="progress-bar bg-<?php echo e($colorDc); ?>"
                                                style="width:<?php echo e(min($pctDc, 100)); ?>%;border-radius:4px;"></div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php
                                    [$pctCrm, $colorCrm] = $kpiPct($customers, $jumlahCustomer ?? 0);
                                ?>
                                <div class="col-6 col-md-4 col-xl">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <div class="avatar avatar-sm">
                                            <div class="avatar-initial bg-label-primary rounded">
                                                <i class="mdi mdi-account-multiple-outline"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0"><?php echo e($customers); ?>

                                                <small class="text-muted fs-tiny">/ <?php echo e($jumlahCustomer); ?></small>
                                            </h6>
                                            <small class="text-muted">CRM Existing</small>
                                        </div>
                                    </div>
                                    <div class="progress" style="height:4px;border-radius:4px;">
                                        <div class="progress-bar bg-<?php echo e($colorCrm); ?>"
                                            style="width:<?php echo e(min($pctCrm, 100)); ?>%;border-radius:4px;"></div>
                                    </div>
                                </div>

                                <?php
                                    [$pctQuote, $colorQuote] = $kpiPct($quotation->count(), $target?->quote ?? 0);
                                ?>
                                <div class="col-6 col-md-4 col-xl">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <div class="avatar avatar-sm">
                                            <div class="avatar-initial bg-label-warning rounded">
                                                <i class="mdi mdi-email-multiple-outline"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0"><?php echo e($quotation->count()); ?>

                                                <?php if($target?->quote): ?>
                                                    <small class="text-muted fs-tiny">/ <?php echo e($target->quote); ?></small>
                                                <?php endif; ?>
                                            </h6>
                                            <small class="text-muted">Quotation</small>
                                        </div>
                                    </div>
                                    <?php if($target?->quote): ?>
                                        <div class="progress" style="height:4px;border-radius:4px;">
                                            <div class="progress-bar bg-<?php echo e($colorQuote); ?>"
                                                style="width:<?php echo e(min($pctQuote, 100)); ?>%;border-radius:4px;"></div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="col-6 col-md-4 col-xl">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <div class="avatar avatar-sm">
                                            <div class="avatar-initial bg-label-success rounded">
                                                <i class="mdi mdi-cart-plus"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0"><?php echo e($po->count()); ?></h6>
                                            <small class="text-muted">Purchase Order</small>
                                        </div>
                                    </div>
                                    <div class="progress" style="height:4px;border-radius:4px;">
                                        <div class="progress-bar bg-<?php echo e($achColor); ?>"
                                            style="width:<?php echo e(min($jumlah_target, 100)); ?>%;border-radius:4px;"></div>
                                    </div>
                                    <small class="text-muted fs-tiny"><?php echo e($formatted_jumlah_target); ?>% target Rp</small>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!--/ Congratulations card -->
            <!-- Total New Leads chart -->
        </div>

        <?php echo $__env->make('pages.sales.dashboard._charts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div class="row gy-4 mb-4">
            
            <div class="col-12" id="hot-prospect-section">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0"><i class="mdi mdi-fire text-danger me-1"></i> Hot Prospect</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="datatable-hot-prospect table table-bordered">
                            <thead>
                                <tr>
                                    <th>Quote No.</th>
                                    <th>Company</th>
                                    <th>Total Price</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>

        


        <div class="card app-calendar-wrapper">
            <div class="row gy-4">
                <!-- Calendar Sidebar -->
                <div class="col app-calendar-sidebar pt-1" id="app-calendar-sidebar">
                    <div class="p-3 pb-2 my-sm-0 mb-3">
                        <div class="d-grid">
                            <button class="btn btn-primary btn-toggle-sidebar" data-bs-toggle="offcanvas"
                                data-bs-target="#addEventSidebar" aria-controls="addEventSidebar">
                                <i class="mdi mdi-plus me-1"></i>
                                <span class="align-middle">Add Event</span>
                            </button>
                        </div>
                    </div>
                    <div class="p-4">
                        <!-- inline calendar (flatpicker) -->
                        <div class="inline-calendar"></div>

                        <hr class="container-m-nx my-4" />

                        <!-- Filter -->
                        <div class="mb-4">
                            <small class="text-small text-muted text-uppercase align-middle">Filter</small>
                        </div>

                        <div class="form-check form-check-secondary mb-3">
                            <input class="form-check-input select-all" type="checkbox" id="selectAll" data-value="all"
                                checked />
                            <label class="form-check-label" for="selectAll">View All</label>
                        </div>

                        <div class="app-calendar-events-filter">
                            <div class="form-check form-check-primary mb-3">
                                <input class="form-check-input input-filter" type="checkbox" id="select-business"
                                    data-value="Business" checked />
                                <label class="form-check-label" for="select-business">Leads</label>
                            </div>
                            <div class="form-check form-check-warning mb-3">
                                <input class="form-check-input input-filter" type="checkbox" id="select-holiday"
                                    data-value="Holiday" checked />
                                <label class="form-check-label" for="select-holiday">Customers</label>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Calendar Sidebar -->

                <!-- Calendar & Modal -->
                <div class="col app-calendar-content">
                    <div class="card shadow-none border-0 border-start rounded-0">
                        <div class="card-body pb-0">
                            <!-- FullCalendar -->
                            <div id="calendar"></div>
                        </div>
                    </div>
                    <div class="app-overlay"></div>
                    <!-- FullCalendar Offcanvas -->
                    <div class="offcanvas offcanvas-end event-sidebar" tabindex="-1" id="addEventSidebar"
                        aria-labelledby="addEventSidebarLabel">
                        <div class="offcanvas-header">
                            <h5 class="offcanvas-title" id="addEventSidebarLabel">Add Event</h5>
                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body">
                            <form class="event-form pt-0" id="eventForm" onsubmit="return false">
                                
                                <div class="form-floating form-floating-outline mb-4 select2-primary">
                                    <select class="select2 select-event-guests form-select" id="eventClient"
                                        name="eventGuests">
                                        <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            
                                            <option value="<?php echo e($client->id); ?>"><?php echo e($client->company); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <label for="eventGuests">Client</label>
                                </div>
                                <div class="form-floating form-floating-outline mb-4">
                                    <input type="text" class="form-control" id="eventStartDate" name="eventStartDate"
                                        placeholder="Start Date" />
                                    <label for="eventStartDate">Date</label>
                                </div>
                                <div class="form-floating form-floating-outline mb-4">
                                    <input type="text" class="form-control" id="eventEndDate" name="eventEndDate"
                                        placeholder="End Date" />
                                    <label for="eventEndDate">Follow Up Date</label>
                                </div>
                                <div class="form-floating form-floating-outline mb-4">
                                    <select class="form-select" id="selectAction" aria-label="Default select example"
                                        name="action">
                                        <option disabled>----- Choose Action -----</option>
                                        <option value="Phone Office">Phone Office</option>
                                        <option value="WhatsApp">WhatsApp</option>
                                    </select>
                                    <label for="selectAction">Action</label>
                                </div>
                                <div class="form-floating form-floating-outline mb-4">
                                    <select class="form-select" id="selectStatus" aria-label="Default select example"
                                        name="status">
                                        <option disabled>----- Choose Status -----</option>
                                        <option value="Responded">Responded</option>
                                        <option value="Not Respon">Not Responded</option>
                                    </select>
                                    <label for="selectStatus">Status</label>
                                </div>
                                <div class="form-floating form-floating-outline mb-4">
                                    <select class="form-select" id="selectIssue" aria-label="Default select example"
                                        name="issues">
                                        <?php $__currentLoopData = $issue; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $issues): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($issues->id); ?>"><?php echo e($issues->issue); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <label for="selectIssue">Status</label>
                                </div>
                                <div class="form-floating form-floating-outline mb-4">
                                    <textarea class="form-control" name="eventNote" id="eventNote"></textarea>
                                    <label for="eventNote">Note</label>
                                </div>
                                <input class="form-control" type="text" name="eventComp" id="eventComp"
                                    value="" hidden>
                                <div class="form-floating mb-4">
                                    <p id="eventNoteBefore"></p>
                                </div>
                                
                                <div class="mb-3 d-flex justify-content-sm-between justify-content-start my-4 gap-2">
                                    <div class="d-flex">
                                        <button type="submit"
                                            class="btn btn-primary btn-add-event me-sm-2 me-1">Add</button>
                                        <button type="reset" class="btn btn-label-secondary btn-cancel me-sm-0 me-1"
                                            data-bs-dismiss="offcanvas">
                                            Cancel
                                        </button>
                                    </div>
                                    
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- /Calendar & Modal -->
            </div>

        </div>

        <?php $__currentLoopData = $prospects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prospect): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo $__env->make('components.modal.prospect.confirm', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


        <!-- Prospect Dashboard -->
    <?php elseif(Auth::user()->role == 'Support'): ?>
        <div class="row g-4 mb-4">
            <!-- Monthly Achievement -->
            <div class="col-12 col-xl-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h4 class="card-title mb-1">Bebenangan Prospek Sasih Ieu 🎉</h4>
                        <p class="text-muted mb-2"><?php echo e(now()->format('F Y')); ?></p>
                        <h4 class="text-primary mb-2"><?php echo e($prospect); ?> Prospek</h4>
                        <?php
                            $progress  = $targetProspect > 0 ? round(($prospect / $targetProspect) * 100, 1) : 0;
                            $remaining = $targetProspect - $prospect;
                        ?>
                        <p class="mb-2">
                            Targetna: <?php echo e($targetProspect); ?> | <?php echo e($progress); ?>% bebenangan
                            (<?php echo e($remaining > 0 ? 'sesa ' . $remaining . ' deui' : 'Alhamdulillah Rengse 🎯'); ?>)
                        </p>
                        <div class="progress" style="height:6px;">
                            <div class="progress-bar <?php echo e($progress >= 80 ? 'bg-success' : ($progress >= 50 ? 'bg-primary' : 'bg-warning')); ?>"
                                 role="progressbar" style="width: <?php echo e($progress); ?>%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KPI GRID -->
            <?php if(Auth::user()->id != '4'): ?>
                <!-- Prospect Masuk -->
                <div class="col-6 col-md-4 col-xl d-flex">
                    <div class="card w-100 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-secondary rounded">
                                        <i class="mdi mdi-account-multiple-plus-outline mdi-24px"></i>
                                    </div>
                                </div>
                                <h3 class="mb-0 fw-semibold"><?php echo e($prospect); ?></h3>
                            </div>
                            <div class="text-muted small mb-2">
                                <?php echo e($diffProspect >= 0 ? '+' . $diffProspect : $diffProspect); ?> ti sasih kamari
                            </div>
                            <div class="mt-auto">
                                <span class="badge bg-label-secondary rounded-pill">Prospek Anu Lebet</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Provided -->
                <div class="col-6 col-md-4 col-xl d-flex">
                    <div class="card w-100 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-info rounded">
                                        <i class="mdi mdi-phone-outline mdi-24px"></i>
                                    </div>
                                </div>
                                <h3 class="mb-0 fw-semibold"><?php echo e($provided); ?></h3>
                            </div>
                            <div class="text-muted small mb-2">
                                <?php echo e($prospect > 0 ? round(($provided / $prospect) * 100, 1) : 0); ?>% ti prospek
                            </div>
                            <div class="mt-auto">
                                <span class="badge bg-label-secondary rounded-pill">Anu Diladangan</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quotation -->
                <div class="col-6 col-md-4 col-xl d-flex">
                    <div class="card w-100 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-warning rounded">
                                        <i class="mdi mdi-email-multiple-outline mdi-24px"></i>
                                    </div>
                                </div>
                                <h3 class="mb-0 fw-semibold"><?php echo e($quotation); ?></h3>
                            </div>
                            <div class="text-muted small mb-2">
                                <?php echo e($prospect > 0 ? round(($quotation / $prospect) * 100, 1) : 0); ?>% ti anu diladangan
                            </div>
                            <div class="mt-auto">
                                <span class="badge bg-label-secondary rounded-pill">Anu Ditawaran</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Purchase Order -->
                <div class="col-6 col-md-4 col-xl d-flex">
                    <div class="card w-100 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-success rounded">
                                        <i class="mdi mdi-cart-plus mdi-24px"></i>
                                    </div>
                                </div>
                                <h3 class="mb-0 fw-semibold"><?php echo e($po); ?></h3>
                            </div>
                            <div class="text-muted small mb-2">
                                <?php echo e($closingRate); ?>% ti anu ditawaran
                            </div>
                            <div class="mt-auto">
                                <span class="badge bg-label-secondary rounded-pill">Anu Jadi Meser</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loss -->
                <div class="col-6 col-md-4 col-xl d-flex">
                    <div class="card w-100 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-danger rounded">
                                        <i class="mdi mdi-close-circle-outline mdi-24px"></i>
                                    </div>
                                </div>
                                <h3 class="mb-0 fw-semibold"><?php echo e($loss); ?></h3>
                            </div>
                            <div class="text-muted small mb-2">
                                <?php echo e($prospect > 0 ? round(($loss / $prospect) * 100, 1) : 0); ?>% ti anu ditawaran
                            </div>
                            <div class="mt-auto">
                                <span class="badge bg-label-secondary rounded-pill">Anu Bedo</span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    <!-- End Support Dashboard -->
<?php elseif(Auth::user()->role == 'Admin'): ?>
    <?php
        $adminView = request()->query('view', $adminView ?? 'sales');
    ?>

    <div class="card clean-card mb-4 p-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-label-primary fs-7 px-3 py-2 rounded-pill">
                    <i class="mdi mdi-shield-account me-1"></i> Admin View
                </span>
                <small class="text-muted fw-semibold d-none d-sm-inline">Pilih Sudut Pandang Dashboard Departemen:</small>
            </div>
            <div class="d-flex flex-wrap gap-1" id="admin-view-switcher">
                <button type="button" data-view="sales"
                   class="btn btn-sm btn-admin-view-switch <?php echo e(($adminView ?? 'sales') === 'sales' ? 'btn-primary shadow-xs' : 'btn-outline-secondary'); ?> rounded-pill px-3 waves-effect">
                    <i class="mdi mdi-chart-line me-1"></i> Sales
                </button>
                <button type="button" data-view="salesmanager"
                   class="btn btn-sm btn-admin-view-switch <?php echo e(($adminView ?? 'sales') === 'salesmanager' ? 'btn-primary shadow-xs' : 'btn-outline-secondary'); ?> rounded-pill px-3 waves-effect">
                    <i class="mdi mdi-account-tie me-1"></i> Sales Manager
                </button>
                <button type="button" data-view="accounting"
                   class="btn btn-sm btn-admin-view-switch <?php echo e(($adminView ?? 'sales') === 'accounting' ? 'btn-primary shadow-xs' : 'btn-outline-secondary'); ?> rounded-pill px-3 waves-effect">
                    <i class="mdi mdi-calculator me-1"></i> Accounting
                </button>
                <button type="button" data-view="finance"
                   class="btn btn-sm btn-admin-view-switch <?php echo e(($adminView ?? 'sales') === 'finance' ? 'btn-primary shadow-xs' : 'btn-outline-secondary'); ?> rounded-pill px-3 waves-effect">
                    <i class="mdi mdi-cash-multiple me-1"></i> Finance
                </button>
                <button type="button" data-view="logistic"
                   class="btn btn-sm btn-admin-view-switch <?php echo e(($adminView ?? 'sales') === 'logistic' ? 'btn-primary shadow-xs' : 'btn-outline-secondary'); ?> rounded-pill px-3 waves-effect">
                    <i class="mdi mdi-truck-delivery-outline me-1"></i> Logistic
                </button>
                <button type="button" data-view="workshop"
                   class="btn btn-sm btn-admin-view-switch <?php echo e(($adminView ?? 'sales') === 'workshop' ? 'btn-primary shadow-xs' : 'btn-outline-secondary'); ?> rounded-pill px-3 waves-effect">
                    <i class="mdi mdi-wrench-outline me-1"></i> Workshop
                </button>
            </div>
        </div>
    </div>

    <div id="admin-view-container" style="transition: opacity 0.2s ease;">
        <?php echo $__env->make('pages.sales.dashboard_view_content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
    
    <?php
        $item = 0;
    ?>
    <?php $__currentLoopData = $dataOverview; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $overview): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('components.modal.overview', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php elseif(Auth::user()->role == 'Logistic'): ?>
    <?php echo $__env->make('pages.logistic.dashboard._content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php elseif(Auth::user()->role == 'Coordinator'): ?>
    <h4 class="fw-3">Request Visit</h4>
    <div class="card mb-3">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-visit-coordinator table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>ID</th>
                        <th>company</th>
                        <th>Machine</th>
                        <th>Date Request</th>
                        <th>Sales</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <h4 class="fw-3">Visit Schedule</h4>
    <div class="card mb-3">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-visit-accept table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>ID</th>
                        <th>company</th>
                        <th>Machine</th>
                        <th>Date</th>
                        <th>Sales</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-notulen table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Desc</th>
                        <th>Level</th>
                        <th>Date</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <?php $__currentLoopData = $visits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $visit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('components.modal.req-visit.form-accept', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php $__currentLoopData = $visited; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $visit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('components.modal.req-visit.form-visited', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php elseif(Auth::user()->role == 'ServiceM'): ?>
    <div class="nav-align-top mb-4">
        <ul class="nav nav-pills mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <button type="button" class="nav-link active waves-effect waves-light" role="tab"
                    data-bs-toggle="tab" data-bs-target="#navs-pills-top-new" aria-controls="navs-pills-top-new"
                    aria-selected="true">
                    New
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button type="button" class="nav-link waves-effect waves-light" role="tab" data-bs-toggle="tab"
                    data-bs-target="#navs-pills-top-progress" aria-controls="navs-pills-top-progress"
                    aria-selected="false" tabindex="-1">
                    Progress
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button type="button" class="nav-link waves-effect waves-light" role="tab" data-bs-toggle="tab"
                    data-bs-target="#navs-pills-top-delivery" aria-controls="navs-pills-top-delivery"
                    aria-selected="false" tabindex="-1">
                    Delivery
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button type="button" class="nav-link waves-effect waves-light" role="tab" data-bs-toggle="tab"
                    data-bs-target="#navs-pills-top-done" aria-controls="navs-pills-top-done" aria-selected="false"
                    tabindex="-1">
                    Done
                </button>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="navs-pills-top-new" role="tabpanel">
                <div class="card-datatable pt-0">
                    <table
                        class="datatable-new-order-search<?php echo e(auth::user()->role == 'Sales' ? '' : '-admin'); ?> table table-bordered">
                        <thead>
                            <?php if(Auth::user()->role == 'Admin' || Auth::user()->role == 'Logistic' || Auth::user()->role == 'ServiceM'): ?>
                                <tr>
                                    <th>No SO</th>
                                    <th>Date</th>
                                    <th>Category</th>
                                    <th>Customer</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>area</th>
                                    <th>Delivery</th>
                                    <th>Sales</th>
                                    <th>Team</th>
                                </tr>
                            <?php endif; ?>
                            <?php if(Auth::user()->role == 'Sales'): ?>
                                <tr>
                                    <th>No SO</th>
                                    <th>Date</th>
                                    <th>PO No.</th>
                                    <th>Customer</th>
                                    <th>Part Desc</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Delivery</th>
                                </tr>
                            <?php endif; ?>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="navs-pills-top-progress" role="tabpanel">

                <div class="card-datatable pt-0">
                    <table
                        class="datatable-sales-list-search<?php echo e(auth::user()->role == 'Sales' ? '' : '-admin'); ?> table table-bordered">
                        <thead>
                            <?php if(Auth::user()->role == 'Admin' || Auth::user()->role == 'Logistic' || Auth::user()->role == 'ServiceM'): ?>
                                <tr>
                                    <th>No SO</th>
                                    <th>Date</th>
                                    <th>Category</th>
                                    <th>Customer</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>area</th>
                                    <th>Delivery</th>
                                    <th>Sales</th>
                                    <th>Team</th>
                                </tr>
                            <?php endif; ?>
                            <?php if(Auth::user()->role == 'Sales'): ?>
                                <tr>
                                    <th>Date</th>
                                    <th>PO No.</th>
                                    <th>Customer</th>
                                    <th>Part Desc</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Delivery</th>
                                </tr>
                            <?php endif; ?>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="navs-pills-top-delivery" role="tabpanel">
                <div class="card-datatable pt-0">
                    <table
                        class="datatable-sales-delivery-search<?php echo e(auth::user()->role == 'Sales' ? '' : '-admin'); ?> table table-bordered">
                        <thead>
                            <tr>
                                <th>PO Date</th>
                                <?php if(Auth::user()->role == 'Sales'): ?>
                                    <th>PO No.</th>
                                <?php endif; ?>
                                <th>Customer</th>
                                <th>Part Desc</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Delivery</th>
                                <?php if(Auth::user()->role == 'Admin' || Auth::user()->role == 'Logistic' || Auth::user()->role == 'ServiceM'): ?>
                                    <th>Sales</th>
                                    <th>Team</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="navs-pills-top-done  " role="tabpanel">
                <div class="card-datatable pt-0">
                    <table
                        class="datatable-sales-completed-search<?php echo e(auth::user()->role == 'Sales' ? '' : '-admin'); ?> table table-bordered">
                        <thead>
                            <tr>
                                <th>PO Date</th>
                                <?php if(Auth::user()->role == 'Sales'): ?>
                                    <th>PO No.</th>
                                <?php endif; ?>
                                <th>Customer</th>
                                <th>Part Desc</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Delivery</th>
                                <?php if(Auth::user()->role == 'Admin' || Auth::user()->role == 'Logistic' || Auth::user()->role == 'ServiceM'): ?>
                                    <th>Sales</th>
                                    <th>Team</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php elseif(Auth::user()->role == 'Technician'): ?>
    <div class="card mb-3">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-notulen table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Desc</th>
                        <th>Level</th>
                        <th>Date</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
<?php elseif(Auth::user()->role == 'Client'): ?>
    <div class="row">
        <div class="col-12">
            <?php if(auth::user()->level == '1'): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5> Machine </h5>
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-client-compressor table table-striped">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Brand</th>
                                        <th>Unit</th>
                                        <th>Tag</th>
                                        <th>Location</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-machine-monitoring table table-striped">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Status</th>
                                        <th>Brand</th>
                                        <th>Type</th>
                                        <th>Unit</th>
                                        <th>SN</th>
                                        <th>PIC</th>
                                        <th>Time</th>
                                        <th>Detail</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-issue-monitoring table table-striped">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Date</th>
                                        <th>Brand</th>
                                        <th>Type</th>
                                        <th>SN</th>
                                        <th>Description</th>
                                        <th>PIC</th>
                                        <th>Accept</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php elseif(Auth::user()->role == 'Accounting'): ?>
    <?php echo $__env->make("pages.accounting.dashboard._content", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php elseif(Auth::user()->role == 'Finance Manager'): ?>
    <?php
        $financeView = $financeView ?? 'finance';
    ?>
    <?php if($financeView === 'accounting'): ?>
        <?php echo $__env->make('pages.accounting.dashboard._content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php elseif($financeView === 'logistic'): ?>
        <?php echo $__env->make('pages.logistic.dashboard._content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php elseif($financeView === 'workshop'): ?>
        <?php echo $__env->make('pages.workshop.dashboard._content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php else: ?>
        <?php echo $__env->make('pages.finance.dashboard._content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
<?php elseif(Auth::user()->role == 'Sales Manager'): ?>
    <?php echo $__env->make('pages.salesmanager.dashboard._content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
    <?php $__currentLoopData = $notulens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notulen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('components.modal.notulen.detail', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('after-style'); ?>
    <style>
        .clean-card {
            border: 1px solid #e7e9ed !important;
            border-radius: 16px !important;
            background: #ffffff !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.015) !important;
            transition: all 0.2s ease-in-out;
        }
        .clean-card:hover {
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.035) !important;
            border-color: rgba(105, 108, 255, 0.25) !important;
        }
        .tooltip-quote-no .tooltip-inner {
            max-width: 320px;
            font-size: 13px;
            padding: 6px 12px;
            letter-spacing: 0.3px;
        }
        table.datatable-hot-prospect td, table.datatable-hot-prospect th { font-size: 14px; }

        /* Welcome Alert (first login of the day) */
        .swal-welcome-popup {
            border-radius: 20px !important;
            padding: 0 0 1.75rem 0 !important;
            overflow: hidden;
        }
        .swal-welcome-popup .swal2-html-container {
            overflow: hidden !important;
        }
        .welcome-alert-header {
            background: linear-gradient(135deg, #696cff 0%, #8f5bff 100%);
            padding: 2.5rem 1.75rem 3.5rem 1.75rem;
            margin: -1.25rem -1.25rem 0 -1.25rem;
            position: relative;
            overflow: hidden;
        }
        .welcome-alert-header::before,
        .welcome-alert-header::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
        }
        .welcome-alert-header::before { width: 140px; height: 140px; top: -60px; right: -40px; }
        .welcome-alert-header::after { width: 90px; height: 90px; bottom: -50px; left: -20px; }
        .welcome-alert-wave {
            display: inline-block;
            font-size: 2.5rem;
            animation: welcome-wave 1.6s infinite;
            transform-origin: 70% 70%;
        }
        .welcome-alert-title {
            color: #fff;
            font-size: 1.5rem;
            font-weight: 700;
            margin-top: 0.5rem;
            position: relative;
            z-index: 1;
        }
        .welcome-alert-subtitle {
            color: rgba(255, 255, 255, 0.85);
            font-size: 1rem;
            position: relative;
            z-index: 1;
        }
        .welcome-alert-body {
            margin-top: -1.85rem;
            padding: 0 1.5rem;
            position: relative;
            z-index: 2;
        }
        .welcome-alert-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
            padding: 1.1rem 1.25rem;
            margin-bottom: 0.9rem;
            display: flex;
            align-items: center;
            text-align: left;
            gap: 1rem;
            text-decoration: none;
            border: 1px solid rgba(0, 0, 0, 0.04);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .welcome-alert-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 22px rgba(0, 0, 0, 0.12);
        }
        .welcome-alert-icon {
            width: 48px;
            height: 48px;
            min-width: 48px;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .welcome-alert-icon.is-calendar { background: rgba(105, 108, 255, 0.12); color: #696cff; }
        .welcome-alert-icon.is-crm { background: rgba(3, 195, 236, 0.12); color: #03c3ec; }
        .welcome-alert-icon.is-quote { background: rgba(113, 221, 55, 0.12); color: #71dd37; }
        .welcome-alert-icon.is-fire { background: rgba(255, 62, 29, 0.12); color: #ff3e1d; animation: welcome-pulse 1.4s infinite; }
        .welcome-alert-card-title { font-weight: 600; font-size: 1rem; color: #2b2c40; margin: 0; }
        .welcome-alert-card-text { font-size: 0.85rem; color: #6c6f80; margin: 0; }
        .welcome-alert-footer {
            margin-top: 0.75rem;
            font-weight: 700;
            font-size: 1.2rem;
            background: linear-gradient(135deg, #696cff, #ff3e1d);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 0.5px;
        }
        @keyframes welcome-wave {
            0%, 60%, 100% { transform: rotate(0deg); }
            10% { transform: rotate(14deg); }
            20% { transform: rotate(-8deg); }
            30% { transform: rotate(14deg); }
            40% { transform: rotate(-4deg); }
            50% { transform: rotate(10deg); }
        }
        @keyframes welcome-pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.12); }
        }
    </style>
    
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet"
        href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/css/pages/app-calendar.css" />

    
    <?php if(Auth::user()->role == 'Sales' || Auth::user()->role == 'Support'): ?>
    <?php endif; ?>

    
    <?php if(Auth::user()->role == 'Admin'): ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/css/pages/cards-statistics.css" />
        <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/css/pages/cards-analytics.css" />
        <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/apex-charts/apex-charts.css" />
    <?php endif; ?>

    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/fullcalendar/fullcalendar.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/flatpickr/flatpickr.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/swiper/swiper.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/quill/editor.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/css/formValidation.min.css" />

    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/animate-css/animate.css" />
<?php $__env->stopPush(); ?>

<?php $__env->startPush('after-script'); ?>
    <!-- Vendors JS -->
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/fullcalendar/fullcalendar.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/moment/moment.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/apex-charts/apexcharts.js"></script>
    
    
    <?php if(Auth::user()->role == 'Sales' || Auth::user()->role == 'Support'): ?>
        <script></script>
    <?php endif; ?>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('page-script'); ?>
    <!-- Page JS -->
    <script src="<?php echo e(asset('assets')); ?>/js/ui-modals.js"></script>

    <?php if(Auth::user()->role == 'Sales' || Auth::user()->role == 'Support'): ?>
        <script src="<?php echo e(asset('assets')); ?>/js/tables-datatables-basic.js"></script>
        <script src="<?php echo e(asset('assets')); ?>/js/dashboards-crm.js"></script>
        <script src="<?php echo e(asset('assets')); ?>/js/app-calendar.js"></script>
        <script src="<?php echo e(asset('assets')); ?>/includes/chart/card-monthly.js"></script>
        <script src="<?php echo e(asset('assets')); ?>/vendor/libs/moment/moment.js"></script>
        <script src="<?php echo e(asset('assets')); ?>/includes/table-req-visit-sales.js"></script>
    <?php endif; ?>

    <?php if(Auth::user()->role == 'Accounting' || (Auth::user()->role == 'Finance Manager' && ($financeView ?? '') === 'accounting') || (Auth::user()->role == 'Admin' && ($adminView ?? '') === 'accounting')): ?>
        <script src="<?php echo e(asset('assets')); ?>/js/app-calendar-accounting.js"></script>
    <?php endif; ?>
    <?php if(Auth::user()->role == 'Coordinator'): ?>
        <script src="<?php echo e(asset('assets')); ?>/includes/table-req-visit-accept.js"></script>
        <script src="<?php echo e(asset('assets')); ?>/includes/table-req-visit-service.js"></script>
    <?php endif; ?>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-hot-prospect.js"></script>
    <?php if(Auth::user()->role == 'Admin'): ?>
        <script src="<?php echo e(asset('assets')); ?>/includes/table-hot-prospect-dashboard.js"></script>
    <?php endif; ?>

    <script src="<?php echo e(asset('assets')); ?>/includes/table-product-sales.js"></script>
    
    <script src="<?php echo e(asset('assets')); ?>/includes/table-product-logistic.js"></script>

    <script src="<?php echo e(asset('assets')); ?>/includes/table-reports-admin.js"></script>

    <script src="<?php echo e(asset('assets')); ?>/includes/table-reports.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-reports-monitor.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-notulen.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-search-sales-list-admin.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-search-new-order-admin.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-search-sales-delivery-admin.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-search-sales-completed-admin.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/app-calendar-events.js"></script>

    <script src="<?php echo e(asset('assets')); ?>/includes/table-client-compressor.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-monitoring-machine-dashboard.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-monitoring-issue-dashboard.js"></script>
    
    <script>
        function formatNumber(n) {
            return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        }

        function validateFloatInputAkurasi(input) {
            let value = input.value;

            // Ganti titik ke koma otomatis
            value = value.replace('.', ',');

            // Hapus karakter selain angka dan koma
            value = value.replace(/[^0-9,]/g, '');

            // Hapus koma lebih dari satu
            let parts = value.split(',');
            if (parts.length > 2) {
                value = parts[0] + ',' + parts[1];
            }

            // Batasi maksimum 5,0
            if (value !== '') {
                let number = parseFloat(value.replace(',', '.'));
                if (!isNaN(number) && number > 5) {
                    value = '5,0';
                }
            }

            input.value = value;

            // Ambil nilai dari kedua input
            let airendEl = document.getElementById('airend');
            let kojishaEl = document.getElementById('kojisha');
            let averageEl = document.getElementById('average');
            let averageTextEl = document.getElementById('averageText');

            if (airendEl && kojishaEl && averageEl) {
                let airend = airendEl.value.replace(',', '.');
                let kojisha = kojishaEl.value.replace(',', '.');

                let a = parseFloat(airend);
                let b = parseFloat(kojisha);

                if (!isNaN(a) && !isNaN(b)) {
                    let avg = (a + b) / 2;
                    let avgStr = avg.toFixed(1).replace('.', ',');
                    averageEl.value = avgStr;
                    averageTextEl.textContent = avgStr;
                } else {
                    averageEl.value = '';
                    averageTextEl.textContent = '';
                }
            }
        }

        function validateFloatInputDelivery(input) {
            let value = input.value;

            // Ganti titik ke koma otomatis
            value = value.replace('.', ',');

            // Hapus karakter selain angka dan koma
            value = value.replace(/[^0-9,]/g, '');

            // Hapus koma lebih dari satu
            let parts = value.split(',');
            if (parts.length > 2) {
                value = parts[0] + ',' + parts[1];
            }

            // Batasi maksimum 5,0
            if (value !== '') {
                let number = parseFloat(value.replace(',', '.'));
                if (!isNaN(number) && number > 5) {
                    value = '5,0';
                }
            }

            input.value = value;

            // Ambil nilai dari kedua input
            let airendEl = document.getElementById('airendDelivery');
            let kojishaEl = document.getElementById('kojishaDelivery');
            let averageEl = document.getElementById('averageDelivery');
            let averageTextEl = document.getElementById('averageDeliveryText');

            if (airendEl && kojishaEl && averageEl) {
                let airend = airendEl.value.replace(',', '.');
                let kojisha = kojishaEl.value.replace(',', '.');

                let a = parseFloat(airend);
                let b = parseFloat(kojisha);

                if (!isNaN(a) && !isNaN(b)) {
                    let avg = (a + b) / 2;
                    let avgStr = avg.toFixed(1).replace('.', ',');
                    averageEl.value = avgStr;
                    averageTextEl.textContent = avgStr;
                } else {
                    averageEl.value = '';
                    averageTextEl.textContent = '';
                }
            }
        }

        function validateFloatInputResponse(input) {
            let value = input.value;

            // Ganti titik ke koma otomatis
            value = value.replace('.', ',');

            // Hapus karakter selain angka dan koma
            value = value.replace(/[^0-9,]/g, '');

            // Hapus koma lebih dari satu
            let parts = value.split(',');
            if (parts.length > 2) {
                value = parts[0] + ',' + parts[1];
            }

            // Batasi maksimum 5,0
            if (value !== '') {
                let number = parseFloat(value.replace(',', '.'));
                if (!isNaN(number) && number > 100) {
                    value = '100';
                }
            }

            input.value = value;

            // Ambil nilai dari kedua input
            let airendEl = document.getElementById('airendResponse');
            let kojishaEl = document.getElementById('kojishaResponse');
            let averageEl = document.getElementById('averageResponse');
            let averageTextEl = document.getElementById('averageResponseText');

            if (airendEl && kojishaEl && averageEl) {
                let airend = airendEl.value.replace(',', '.');
                let kojisha = kojishaEl.value.replace(',', '.');

                let a = parseFloat(airend);
                let b = parseFloat(kojisha);

                if (!isNaN(a) && !isNaN(b)) {
                    let avg = (a + b) / 2;
                    let avgStr = avg.toFixed(1).replace('.', ',');
                    averageEl.value = avgStr;
                    averageTextEl.textContent = avgStr + '%';
                } else {
                    averageEl.value = '';
                    averageTextEl.textContent = '';
                }
            }
        }

        function validateFloatInputRating(input) {
            let value = input.value;

            // Ganti titik ke koma otomatis
            value = value.replace('.', ',');

            // Hapus karakter selain angka dan koma
            value = value.replace(/[^0-9,]/g, '');

            // Hapus koma lebih dari satu
            let parts = value.split(',');
            if (parts.length > 2) {
                value = parts[0] + ',' + parts[1];
            }

            // Batasi maksimum 5,0
            if (value !== '') {
                let number = parseFloat(value.replace(',', '.'));
                if (!isNaN(number) && number > 5) {
                    value = '5,0';
                }
            }

            input.value = value;

            // Ambil nilai dari kedua input
            let airendEl = document.getElementById('airendRating');
            let kojishaEl = document.getElementById('kojishaRating');
            let averageEl = document.getElementById('averageRating');
            let averageTextEl = document.getElementById('averageRatingText');

            if (airendEl && kojishaEl && averageEl) {
                let airend = airendEl.value.replace(',', '.');
                let kojisha = kojishaEl.value.replace(',', '.');

                let a = parseFloat(airend);
                let b = parseFloat(kojisha);

                if (!isNaN(a) && !isNaN(b)) {
                    let avg = (a + b) / 2;
                    let avgStr = avg.toFixed(1).replace('.', ',');
                    averageEl.value = avgStr;
                    averageTextEl.textContent = avgStr;
                } else {
                    averageEl.value = '';
                    averageTextEl.textContent = '';
                }
            }
        }

        function validateFloatInputCustomer(input) {
            let value = input.value;

            // Ganti titik ke koma otomatis
            value = value.replace('.', ',');

            // Hapus karakter selain angka dan koma
            value = value.replace(/[^0-9,]/g, '');

            // Hapus koma lebih dari satu
            let parts = value.split(',');
            if (parts.length > 2) {
                value = parts[0] + ',' + parts[1];
            }

            // Batasi maksimum 5,0
            if (value !== '') {
                let number = parseFloat(value.replace(',', '.'));
                if (!isNaN(number) && number > 5) {
                    value = '5,0';
                }
            }

            input.value = value;

            // Ambil nilai dari kedua input
            let airendEl = document.getElementById('airendCustomer');
            let kojishaEl = document.getElementById('kojishaCustomer');
            let averageEl = document.getElementById('averageCustomer');
            let averageTextEl = document.getElementById('averageCustomerText');

            if (airendEl && kojishaEl && averageEl) {
                let airend = airendEl.value.replace(',', '.');
                let kojisha = kojishaEl.value.replace(',', '.');

                let a = parseFloat(airend);
                let b = parseFloat(kojisha);

                if (!isNaN(a) && !isNaN(b)) {
                    let avg = (a + b) / 2;
                    let avgStr = avg.toFixed(1).replace('.', ',');
                    averageEl.value = avgStr;
                    averageTextEl.textContent = avgStr;
                } else {
                    averageEl.value = '';
                    averageTextEl.textContent = '';
                }
            }
        }

        function validateMaxInput(input) {
            let value = input.value;

            // Ganti titik ke koma otomatis
            value = value.replace('.', ',');

            // Hapus karakter selain angka dan koma
            value = value.replace(/[^0-9,]/g, '');

            // Hapus koma lebih dari satu
            let parts = value.split(',');
            if (parts.length > 2) {
                value = parts[0] + ',' + parts[1];
            }

            // Batasi maksimum 5,0
            if (value !== '') {
                let number = parseFloat(value.replace(',', '.'));
                if (!isNaN(number) && number > 3) {
                    value = 3;
                }
            }

            input.value = value;
        }

        $('.change-sales').on('click', function(ev) {
            var id = $(this).data('id');
            console.log('sales ini ber id : ' + id);
            var $pane = $('#navs-sales-' + id);

            // Ajax Sales Kiri
            $.ajax({
                url: '/dashboard/filteredLeads/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.filtered-leads').text(response);
                }
            });
            $.ajax({
                url: '/dashboard/filteredPercentLeads/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.filtered-percent-leads').text(response + '%');
                }
            });
            $.ajax({
                url: '/dashboard/filteredTargetLeads/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.filtered-target-leads').text('/ ' + response.leads);
                    console.log(response.leads);
                }
            });
            $.ajax({
                url: '/dashboard/filteredDc/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.filtered-dc').text(response);
                }
            });
            $.ajax({
                url: '/dashboard/filteredPercentDc/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.filtered-percent-dc').text(response + '%');
                }
            });
            $.ajax({
                url: '/dashboard/filteredTargetDc/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.filtered-target-dc').text('/ ' + response.dc);
                }
            });
            $.ajax({
                url: '/dashboard/filteredCRM/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.filtered-crm').text(response);
                }
            });
            $.ajax({
                url: '/dashboard/filteredPercentCRM/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.filtered-percent-crm').text(response + ' %');
                }
            });
            $.ajax({
                url: '/dashboard/filteredTargetCRM/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.filtered-target-crm').text('/ ' + response);
                }
            });
            $.ajax({
                url: '/dashboard/filteredQuote/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.filtered-quote').text(response);
                }
            });
            $.ajax({
                url: '/dashboard/filteredPercentQuote/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.filtered-percent-quote').text(response + '%');
                }
            });
            $.ajax({
                url: '/dashboard/filteredTargetQuote/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.filtered-target-quote').text('/ ' + response.quote);
                }
            });
            $.ajax({
                url: '/dashboard/filteredProspectAdmin/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.filtered-prospect-sales').text(response);
                }
            });
            $.ajax({
                url: '/dashboard/filteredPercentProspectAdmin/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.filtered-percent-prospect-sales').text(response + '%');
                }
            });
            $.ajax({
                url: '/dashboard/filteredAllProspect/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.filtered-all-prospect').text('/ ' + response);
                }
            });

            // Ajax Sales Kanan
            $.ajax({
                url: '/dashboard/totalQuotation/' + id,
                type: 'GET',
                success: function(response) {
                    total = formatNumber(response);
                    $pane.find('.admin-total-quotation').text('Rp ' + total);
                }
            });
            $.ajax({
                url: '/dashboard/totalProspect/' + id,
                type: 'GET',
                success: function(response) {
                    total = formatNumber(response);
                    $pane.find('.admin-total-prospect').text('Rp ' + total);
                }
            });
            $.ajax({
                url: '/dashboard/totalHotProspect/' + id,
                type: 'GET',
                success: function(response) {
                    total = formatNumber(response);
                    $pane.find('.admin-total-hot-prospect').text('Rp ' + total);
                }
            });
            $.ajax({
                url: '/dashboard/totalLoss/' + id,
                type: 'GET',
                success: function(response) {
                    total = formatNumber(response);
                    $pane.find('.admin-total-loss').text('Rp ' + total);
                }
            });
            $.ajax({
                url: '/dashboard/totalPo/' + id,
                type: 'GET',
                success: function(response) {
                    total = formatNumber(response);
                    $pane.find('.admin-total-po').text('Rp ' + total);
                }
            });
            $.ajax({
                url: '/dashboard/totalTargetPo/' + id,
                type: 'GET',
                success: function(response) {
                    total = formatNumber(response);
                    let color = 'danger';
                    if (response > 80 && response <= 100) {
                        color = 'warning';
                    } else if (response > 100) {
                        color = 'success';
                    }

                    // Update class
                    const $el = $pane.find('.admin-target-total-po');
                    $el.removeClass(
                            'bg-label-danger bg-label-warning bg-label-success')
                        .addClass(`bg-label-${color}`);
                    $pane.find('.admin-target-total-po').text(response + ' %');
                }
            });

            $.ajax({
                url: '/dashboard/target/' + id,
                type: 'GET',
                success: function(response) {
                    var targetPercentage = (response / 100).toFixed(3);
                    $pane.find('.target-po').text(targetPercentage + '%');
                    console.log(targetPercentage);
                }
            });
            $.ajax({
                url: '/dashboard/filteredProspectQuote/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.filtered-prospect-quotation').text(response);
                }
            });
            $.ajax({
                url: '/dashboard/filteredProspectPO/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.filtered-prospect-po').text(response);
                }
            });
            $.ajax({
                url: '/dashboard/totalProspectPO/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.total-prospect-po').text('Rp ' + response);
                }
            });
            // Ajax Online Sales
            $.ajax({
                url: '/dashboard/filteredProduct/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.filtered-product').text(response);
                    $pane.find('.filtered-percent-product').text(response + ' %');
                }
            });
            $.ajax({
                url: '/dashboard/filteredSW/' + id,
                type: 'GET',
                success: function(response) {
                    const value = parseFloat(response) || 0;
                    $pane.find('.filtered-sw').text(value);

                    let percent;
                    if (id === 16) {
                        percent = (value / 120) * 100;
                    } else {
                        percent = (value / 60) * 100;
                    }

                    $pane.find('.filtered-percent-sw').text(percent.toFixed(1) + ' %');
                }
            });
            $.ajax({
                url: '/dashboard/filteredVideo/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.filtered-video').text(response);
                    $pane.find('.filtered-percent-video').text(response + ' %');
                }
            });
            $.ajax({
                url: '/dashboard/filteredStat/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.filtered-status').text(response);
                    const value = parseFloat(response) || 0;
                    let percent;
                    percent = value / 5 * 100;
                    $pane.find('.filtered-percent-status').text(percent.toFixed(1) + ' %');
                }
            });
            $.ajax({
                url: '/dashboard/filteredDelivery/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.filtered-delivery').text(response);
                    const value = parseFloat(response) || 0;
                    let percent;
                    percent = value / 5 * 100;
                    $pane.find('.filtered-percent-delivery').text(percent.toFixed(1) + ' %');
                }
            });
            $.ajax({
                url: '/dashboard/filteredCustomer/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.filtered-customer').text(response);
                    const value = parseFloat(response) || 0;
                    let percent;
                    percent = value / 5 * 100;
                    $pane.find('.filtered-percent-customer').text(percent.toFixed(1) + ' %');
                }
            });
            $.ajax({
                url: '/dashboard/filteredResponse/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.filtered-response').text(response);
                    $pane.find('.filtered-percent-response').text(response + ' %');
                }
            });
            $.ajax({
                url: '/dashboard/filteredRating/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.filtered-rating').text(response);
                    const value = parseFloat(response) || 0;
                    let percent;
                    percent = value / 5 * 100;
                    $pane.find('.filtered-percent-rating').text(percent.toFixed(1) + ' %');
                }
            });

            // Ajax Support
            $.ajax({
                url: '/dashboard/filteredProspect/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.filtered-prospect').text(response);
                    $pane.find('.filtered-percent-prospect').text(response + ' %');
                }
            });
            $.ajax({
                url: '/dashboard/filteredProvide/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.filtered-provided').text(response.provide);
                    $pane.find('.filtered-percent-provided').text(response.percent + ' %');
                    $pane.find('.filtered-all-prospect-provided').text(response.prospect);
                }
            });
            $.ajax({
                url: '/dashboard/filteredProspectQuote/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.filtered-quote-prospect').text(response.quotation);
                    $pane.find('.filtered-percent-quote-prospect').text(response.percent + ' %');
                    $pane.find('.filtered-all-quote-prospect').text(response.provide);
                }
            });
            $.ajax({
                url: '/dashboard/filteredNotProvide/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.filtered-not-provided').text(response.provide);
                    $pane.find('.filtered-percent-not-provided').text(response.percent + ' %');
                    $pane.find('.filtered-all-prospect-not-provided').text(response.prospect);
                }
            });
            $.ajax({
                url: '/dashboard/filteredProspectPO/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.filtered-po-prospect').text(response.po);
                    $pane.find('.filtered-percent-po-prospect').text(response.percent + ' %');
                    $pane.find('.filtered-all-po-prospect').text(response.quotation);
                }
            });
            $.ajax({
                url: '/dashboard/totalProspectQuote/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.total-prospect-quotation').text('Rp ' + response);
                }
            });
            $.ajax({
                url: '/dashboard/totalProspectProspect/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.total-prospect-hot').text('Rp ' + response);
                }
            });
            $.ajax({
                url: '/dashboard/totalProspectPO/' + id,
                type: 'GET',
                success: function(response) {
                    $pane.find('.total-prospect-po').text('Rp ' + response);
                }
            });

        });

        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
            $('.checkPlanning').on('change', function() {
                let isChecked = $(this).is(':checked');

                // Tambah atau hapus class alert-success
                if (isChecked) {
                    $('.alert-planning').addClass('alert-success');
                } else {
                    $('.alert-planning').removeClass('alert-success');
                }

                $.ajax({
                    url: '/check-planning', // sesuaikan dengan route kamu
                    type: 'POST',
                    data: {
                        planing: isChecked ? 1 : 0,
                        _token: '<?php echo e(csrf_token()); ?>'
                    },
                    success: function(response) {
                        console.log('Planning status updated:', response);
                    },
                    error: function(xhr) {
                        console.error('Terjadi error:', xhr.responseText);
                    }
                });
            });

            $('.checkSync').on('change', function() {
                let isChecked = $(this).is(':checked');

                // Tambah atau hapus class alert-success
                if (isChecked) {
                    $('.alert-sync').addClass('alert-success');
                } else {
                    $('.alert-sync').removeClass('alert-success');
                }

                $.ajax({
                    url: '/check-sync', // sesuaikan dengan route kamu
                    type: 'POST',
                    data: {
                        sync: isChecked ? 1 : 0,
                        _token: '<?php echo e(csrf_token()); ?>'
                    },
                    success: function(response) {
                        console.log('sync status updated:', response);
                    },
                    error: function(xhr) {
                        console.error('Terjadi error:', xhr.responseText);
                    }
                });
            });

            $('.checkAbnormal').on('change', function() {
                let isChecked = $(this).is(':checked');

                // Tambah atau hapus class alert-success
                if (isChecked) {
                    $('.alert-abnormal').addClass('alert-success');
                } else {
                    $('.alert-abnormal').removeClass('alert-success');
                }

                $.ajax({
                    url: '/check-abnormal', // sesuaikan dengan route kamu
                    type: 'POST',
                    data: {
                        abnormal: isChecked ? 1 : 0,
                        _token: '<?php echo e(csrf_token()); ?>'
                    },
                    success: function(response) {
                        console.log('abnormal status updated:', response);
                    },
                    error: function(xhr) {
                        console.error('Terjadi error:', xhr.responseText);
                    }
                });
            });

            $('.checkLog').on('change', function() {
                let isChecked = $(this).is(':checked');

                // Tambah atau hapus class alert-success
                if (isChecked) {
                    $('.alert-log').addClass('alert-success');
                } else {
                    $('.alert-log').removeClass('alert-success');
                }

                $.ajax({
                    url: '/check-log', // sesuaikan dengan route kamu
                    type: 'POST',
                    data: {
                        log: isChecked ? 1 : 0,
                        _token: '<?php echo e(csrf_token()); ?>'
                    },
                    success: function(response) {
                        console.log('log status updated:', response);
                    },
                    error: function(xhr) {
                        console.error('Terjadi error:', xhr.responseText);
                    }
                });
            });

            $('.checkTimeline').on('change', function() {
                let isChecked = $(this).is(':checked');

                // Tambah atau hapus class alert-success
                if (isChecked) {
                    $('.alert-timeline').addClass('alert-success');
                } else {
                    $('.alert-timeline').removeClass('alert-success');
                }

                $.ajax({
                    url: '/check-timeline', // sesuaikan dengan route kamu
                    type: 'POST',
                    data: {
                        timeline: isChecked ? 1 : 0,
                        _token: '<?php echo e(csrf_token()); ?>'
                    },
                    success: function(response) {
                        console.log('timeline status updated:', response);
                    },
                    error: function(xhr) {
                        console.error('Terjadi error:', xhr.responseText);
                    }
                });
            });

            $('.checkPreventive').on('change', function() {
                let isChecked = $(this).is(':checked');

                // Tambah atau hapus class alert-success
                if (isChecked) {
                    $('.alert-preventive').addClass('alert-success');
                } else {
                    $('.alert-preventive').removeClass('alert-success');
                }

                $.ajax({
                    url: '/check-preventive', // sesuaikan dengan route kamu
                    type: 'POST',
                    data: {
                        preventive: isChecked ? 1 : 0,
                        _token: '<?php echo e(csrf_token()); ?>'
                    },
                    success: function(response) {
                        console.log('preventive status updated:', response);
                    },
                    error: function(xhr) {
                        console.error('Terjadi error:', xhr.responseText);
                    }
                });
            });

            $(document).on('click', '.accept-issue', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, Accept it!",
                    customClass: {
                        confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                        cancelButton: "btn btn-label-secondary waves-effect",
                    },
                    buttonsStyling: false,
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            'url': '<?php echo e(url('monitoring-client')); ?>/accept-issue/' + id,
                            'type': 'POST',
                            'data': {
                                '_token': '<?php echo e(csrf_token()); ?>'
                            },
                            success: function(response) {
                                if (response == 1) {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Accepted!",
                                        text: "Your file has been Accepted.",
                                        customClass: {
                                            confirmButton: "btn btn-success waves-effect",
                                        },
                                    })
                                    window.setTimeout(function() {
                                        window.location.href = '/';
                                    }, 2000);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Data Failed to Accept!'
                                    });
                                }
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire({
                            title: "Cancelled",
                            text: "You Cancel Accept :)",
                            icon: "error",
                            customClass: {
                                confirmButton: "btn btn-success waves-effect",
                            },
                        });
                    }
                });
            });

        });
    </script>
    
    

    <?php if($showWelcomeAlert ?? false): ?>
        <script>
            $(document).ready(function() {
                Swal.fire({
                    html: `
                        <div class="welcome-alert-header">
                            <span class="welcome-alert-wave">👋</span>
                            <div class="welcome-alert-title">Hai <?php echo e(Auth::user()->name); ?>!</div>
                            <div class="welcome-alert-subtitle">Semangat menjalani hari ini ya</div>
                        </div>
                        <div class="welcome-alert-body">
                            <?php if(Auth::user()->id == 4): ?>
                                <a href="#" id="swal-goto-crm" class="welcome-alert-card">
                                    <div class="welcome-alert-icon is-crm"><i class="mdi mdi-account-star-outline"></i></div>
                                    <div>
                                        <p class="welcome-alert-card-title"><?php echo e($crmPotensialCount ?? 0); ?> CRM Potensial Perlu Di-follow Up</p>
                                        <p class="welcome-alert-card-text">Follow up customer CRM potensialmu hari ini</p>
                                    </div>
                                </a>
                            <?php else: ?>
                                <a href="#" id="swal-goto-calendar" class="welcome-alert-card">
                                    <div class="welcome-alert-icon is-calendar"><i class="mdi mdi-account-search-outline"></i></div>
                                    <div>
                                        <p class="welcome-alert-card-title"><?php echo e($activeLeadsCount ?? 0); ?> Leads Perlu Di-follow Up</p>
                                        <p class="welcome-alert-card-text">Jangan lupa cek jadwal follow up leads hari ini</p>
                                    </div>
                                </a>
                            <?php endif; ?>

                            <a href="#" id="swal-goto-quotations" class="welcome-alert-card">
                                <div class="welcome-alert-icon is-quote"><i class="mdi mdi-file-document-outline"></i></div>
                                <div>
                                    <p class="welcome-alert-card-title"><?php echo e($totalActiveQuotationCount ?? 0); ?> Penawaran Aktif (Belum PO)</p>
                                    <p class="welcome-alert-card-text">Pantau terus perkembangan penawaran yang sedang berjalan</p>
                                </div>
                            </a>

                            <a href="#" id="swal-goto-hot-prospect" class="welcome-alert-card">
                                <div class="welcome-alert-icon is-fire"><i class="mdi mdi-fire"></i></div>
                                <div>
                                    <?php if(($totalHotProspectCount ?? 0) > 0): ?>
                                        <p class="welcome-alert-card-title">Rp <?php echo e(number_format($totalHotProspectNominal ?? 0, 0, ',', '.')); ?> Hot Prospect</p>
                                        <p class="welcome-alert-card-text">Sebentar lagi mau PO, jangan sampai lewat</p>
                                    <?php else: ?>
                                        <p class="welcome-alert-card-title">Belum Ada Hot Prospect</p>
                                        <p class="welcome-alert-card-text">Terus semangat cari peluang baru!</p>
                                    <?php endif; ?>
                                </div>
                            </a>
                            <div class="welcome-alert-footer">Fighting!!! 🔥</div>
                        </div>
                    `,
                    width: '44rem',
                    showClass: {
                        popup: 'animate__animated animate__zoomIn animate__faster',
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp animate__faster',
                    },
                    confirmButtonText: 'Siap, Gaskeun! 🚀',
                    customClass: {
                        popup: 'swal-welcome-popup',
                        confirmButton: 'btn btn-primary waves-effect waves-light',
                    },
                    buttonsStyling: false,
                    didOpen: function() {
                        var gotoCrm = document.getElementById('swal-goto-crm');
                        if (gotoCrm) {
                            gotoCrm.addEventListener('click', function(e) {
                                e.preventDefault();
                                Swal.close();
                                var target = document.getElementById('crm-section') || document.getElementById('calendar');
                                if (target) target.scrollIntoView({ behavior: 'smooth' });
                            });
                        }
                        var gotoCalendar = document.getElementById('swal-goto-calendar');
                        if (gotoCalendar) {
                            gotoCalendar.addEventListener('click', function(e) {
                                e.preventDefault();
                                Swal.close();
                                var target = document.getElementById('calendar');
                                if (target) target.scrollIntoView({ behavior: 'smooth' });
                            });
                        }
                        var gotoQuotations = document.getElementById('swal-goto-quotations');
                        if (gotoQuotations) {
                            gotoQuotations.addEventListener('click', function(e) {
                                e.preventDefault();
                                Swal.close();
                                var target = document.getElementById('hot-prospect-section') || document.getElementById('calendar');
                                if (target) target.scrollIntoView({ behavior: 'smooth' });
                            });
                        }
                        var gotoHotProspect = document.getElementById('swal-goto-hot-prospect');
                        if (gotoHotProspect) {
                            gotoHotProspect.addEventListener('click', function(e) {
                                e.preventDefault();
                                Swal.close();
                                var target = document.getElementById('hot-prospect-section') || document.getElementById('calendar');
                                if (target) target.scrollIntoView({ behavior: 'smooth' });
                            });
                        }
                    },
                });
            });
        </script>
    <?php endif; ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const container = document.getElementById('admin-view-container');
                const buttons = document.querySelectorAll('.btn-admin-view-switch');
                if (!container || !buttons.length) return;

                const viewCache = {};
                const currentView = '<?php echo e($adminView ?? "sales"); ?>';
                viewCache[currentView] = container.innerHTML;

                buttons.forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        const selectedView = this.getAttribute('data-view');

                        buttons.forEach(b => {
                            b.classList.remove('btn-primary', 'shadow-xs');
                            b.classList.add('btn-outline-secondary');
                        });
                        this.classList.remove('btn-outline-secondary');
                        this.classList.add('btn-primary', 'shadow-xs');

                        const url = new URL(window.location.href);
                        url.searchParams.set('view', selectedView);
                        window.history.pushState({ view: selectedView }, '', url.toString());

                        if (viewCache[selectedView]) {
                            container.innerHTML = viewCache[selectedView];
                            reinitDataTable();
                            return;
                        }

                        container.style.opacity = '0.5';
                        container.innerHTML = `
                            <div class="card clean-card p-5 text-center my-4">
                                <div class="spinner-border text-primary mx-auto mb-3" role="status" style="width: 2.5rem; height: 2.5rem;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Memuat Dashboard ${selectedView.toUpperCase()}...</h6>
                                <small class="text-muted">Sedang mengunduh data divisi secara cepat tanpa reload halaman.</small>
                            </div>
                        `;

                        fetch(`<?php echo e(route('dashboard.ajax-view')); ?>?view=${selectedView}`)
                            .then(res => res.json())
                            .then(data => {
                                container.style.opacity = '1';
                                if (data.status === 'success' && data.html) {
                                    viewCache[selectedView] = data.html;
                                    container.innerHTML = data.html;
                                    reinitDataTable();
                                } else {
                                    container.innerHTML = `<div class="alert alert-danger m-3">Gagal memuat data dashboard ${selectedView}.</div>`;
                                }
                            })
                            .catch(err => {
                                container.style.opacity = '1';
                                console.error('AJAX View Error:', err);
                                container.innerHTML = `<div class="alert alert-danger m-3">Terjadi kesalahan koneksi saat memuat dashboard.</div>`;
                            });
                    });
                });

                function reinitDataTable() {
                    if (window.jQuery && $.fn.DataTable) {
                        $('.datatable-prospect-quote:not(.dataTable)').DataTable();
                    }
                }
            });
        </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/sales/dashboard.blade.php ENDPATH**/ ?>