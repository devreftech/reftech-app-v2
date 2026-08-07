<!DOCTYPE html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="../../assets/" data-template="vertical-menu-template">

<head>
    <?php echo $__env->make('includes.sales.meta', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php $__env->startSection('title', 'Monitoring Visit'); ?>
    <?php echo $__env->make('includes.sales.style', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />


    
    <script src="<?php echo e(asset('/assets')); ?>/vendor/js/helpers.js"></script>

    
    
    <script src="<?php echo e(asset('/assets')); ?>/vendor/js/template-customizer.js"></script>

    
    <script src="<?php echo e(asset('assets')); ?>/js/config.js"></script>
    <?php echo app('Tightenco\Ziggy\BladeRouteGenerator')->generate(); ?>
</head>

<body>
    <?php
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    ?>
    <!--  Layout wrapper  -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="container-fluid flex-grow-1 container-p-y">
                <div class="container">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-row flex-column">
                                <div class="mb-xl-0 pb-1">
                                    <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                                        <span class="app-brand-logo demo">
                                            <span style="color: var(--bs-primary)">
                                                <img class="text-md"
                                                    src="<?php echo e(url('https://reftech.id/wp-content/uploads/2021/10/Reftech-Logo-Hitam.png')); ?>"
                                                    alt="" srcset="" width="60%">
                                            </span>
                                        </span>
                                    </div>
                                    <p class="mb-1 fw-bolder">PT Reftech Jaya Optima</p>
                                    <div style="font-size: 10px">
                                        <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville No. 31</p>
                                        <p class="mb-1">Bandung – Jawa Barat 40218</p>
                                        <p class="mb-1">
                                            <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022
                                            54417653<?php echo e('  |  '); ?><i
                                                class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@reftech.id
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="fw-bold">DAILY MONITORING</h3>
                                    <div>
                                        <span class="fw-bolder"><?php echo e($machine->unit->unit->unit); ?></span>
                                    </div>
                                    <div class="mt-1">
                                        <span class="fw-bold"><?php echo e($machine->unit->brand); ?>

                                            <?php echo e($machine->unit->unit->sku); ?></span>
                                    </div>
                                    <div class="mt-1 mb-4">
                                        <span class="text-muted"><?php echo e($machine->tag); ?> - <?php echo e($machine->location); ?></span>
                                    </div>
                                    <div class="btn-group">
                                        <button type="button"
                                            class="btn btn-outline-primary dropdown-toggle waves-effect waves-light"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            Month
                                        </button>
                                        <ul class="dropdown-menu" style="">
                                            <?php for($i = 1; $i < 13; $i++): ?>
                                                <li>
                                                    <a class="dropdown-item waves-effect"
                                                        href="<?php echo e(route('visitor-change.daily-monitoring', [$machine->id,$i])); ?>">Month
                                                        <?php echo e($months[$i]); ?></a>
                                                </li>
                                            <?php endfor; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="daily mb-4">
                                <h4>Daily Check</h4>
                                <div class="table-responsive text-nowrap mt-4">
                                    <?php if($machine->unit->unit->unit != 'REFRIGERANT AIR DRYER'): ?>
                                        <table class="table table-bordered">
                                            <thead class="table-light" align="center">
                                                <th style="vertical-align: middle;">Date</th>
                                                <th style="vertical-align: middle;">Condition</th>
                                                <th style="vertical-align: middle;">Running<br>Hour</th>
                                                <th style="vertical-align: middle;">Load Hour</th>
                                                <th style="vertical-align: middle;">Press.</th>
                                                <th style="vertical-align: middle;">Temp.<br>(85°C - 94°C)</th>
                                                <th style="vertical-align: middle;">Oil Level</th>
                                                <th style="vertical-align: middle;">Kebocoran</th>
                                                <th style="vertical-align: middle;">PIC</th>
                                            </thead>
                                            <tbody>
                                                <?php $__currentLoopData = $compressor; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr align="center">
                                                        <td><?php echo e($item['date']); ?></td>
                                                        <td><?php echo e($item['condition']); ?></td>
                                                        <td><?php echo e($item['running']); ?></td>
                                                        <td><?php echo e($item['loading']); ?></td>
                                                        <td><?php echo e($item['pressure']); ?></td>
                                                        <td>
                                                            <?php
                                                                $stringTemp = $item['temp'] ?? ''; // Pastikan nilai tidak null
                                                                $tempNumber = null;

                                                                if (
                                                                    preg_match('/\d+(\.\d+)?/', $stringTemp, $matches)
                                                                ) {
                                                                    $tempNumber = (float) $matches[0]; // Gunakan float agar mendukung desimal
                                                                }
                                                            ?>

                                                            <?php if(!is_null($tempNumber) && $tempNumber > 94): ?>
                                                                <p class="mb-0 fw-bold fs-6 text-danger">
                                                                    <?php echo e($item['temp']); ?></p>
                                                            <?php else: ?>
                                                                <?php echo e($item['temp']); ?>

                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo e($item['oil_level']); ?></td>
                                                        <td><?php echo e($item['leak']); ?></td>
                                                        <td><?php echo e($item['pic']); ?></td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    <?php else: ?>
                                        <table class="table table-bordered">
                                            <thead class="table-light" align="center">
                                                <th style="vertical-align: middle;">Date</th>
                                                <th style="vertical-align: middle;">Condition</th>
                                                <th style="vertical-align: middle;">Temp. IN</th>
                                                <th style="vertical-align: middle;">Temp. OUT</th>
                                                <th style="vertical-align: middle;">Dewpoint</th>
                                                <th style="vertical-align: middle;">Auto Drain</th>
                                                <th style="vertical-align: middle;">Fan<br>Condensor</th>
                                                <th style="vertical-align: middle;">Kebocoran</th>
                                                <th style="vertical-align: middle;">PIC</th>
                                            </thead>
                                            <tbody>
                                                <?php $__currentLoopData = $dryer; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr align="center">
                                                        <td><?php echo e($item['date']); ?></td>
                                                        <td><?php echo e($item['condition']); ?></td>
                                                        <td><?php echo e($item['temp']); ?></td>
                                                        <td><?php echo e($item['temp_out']); ?></td>
                                                        <td>
                                                            <?php
                                                                $stringDew = $item['dew'] ?? ''; // Pastikan nilai tidak null
                                                                $dewNumber = null;

                                                                if (
                                                                    preg_match('/\d+(\.\d+)?/', $stringDew, $matches)
                                                                ) {
                                                                    $dewNumber = (float) $matches[0]; // Gunakan float agar mendukung desimal
                                                                }
                                                            ?>
                                                            <?php if(!is_null($dewNumber) && $dewNumber > 10): ?>
                                                                <p class="mb-0 fw-bold fs-6 text-danger">
                                                                    <?php echo e($item['dew']); ?></p>
                                                            <?php else: ?>
                                                                <?php echo e($item['dew']); ?>

                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo e($item['drain']); ?></td>
                                                        <td><?php echo e($item['fan']); ?></td>
                                                        <td><?php echo e($item['leak']); ?></td>
                                                        <td><?php echo e($item['pic']); ?></td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="weekly mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h4>Weekly Check</h4>
                                <div class="table-responsive text-nowrap mb-4">
                                    <table class="table table-bordered">
                                        <?php if($machine->unit->unit->unit != 'REFRIGERANT AIR DRYER'): ?>
                                            <thead class="table-light" align="center">
                                                <th style="vertical-align: middle;">Week</th>
                                                <th style="vertical-align: middle;">Condition</th>
                                                <th style="vertical-align: middle;">Vibration<br>(mm/s)</th>
                                                <th style="vertical-align: middle;">Voltage (V)</th>
                                                <th style="vertical-align: middle;">Ampere (A)</th>
                                                <th style="vertical-align: middle;">Cleaning<br>Cooler</th>
                                                <th style="vertical-align: middle;">Cek Coupling<br>/ Belt</th>
                                                <th style="vertical-align: middle;">Cleaning<br>Compressor & Area</th>
                                                <th style="vertical-align: middle;">PIC</th>
                                            </thead>
                                            <tbody align="center">
                                                <?php
                                                    $noWeek = 1;
                                                ?>
                                                <?php $__empty_1 = true; $__currentLoopData = $weeksoy; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $monweek): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                    <tr>
                                                        <td><?php echo e($noWeek); ?></td>
                                                        <td><?php echo e($monweek['condition']); ?></td>
                                                        <td><?php echo e($monweek['vibration']); ?></td>
                                                        <td><?php echo e($monweek['voltage']); ?></td>
                                                        <td><?php echo e($monweek['ampere']); ?></td>
                                                        <td>
                                                            <?php if($monweek['cooler'] == 1): ?>
                                                                
                                                                cleaning
                                                            <?php else: ?>
                                                                
                                                                -
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if($monweek['coupling'] == 1): ?>
                                                                
                                                                Ok
                                                            <?php elseif($monweek['coupling'] == 0): ?>
                                                                
                                                                Not Ok
                                                            <?php else: ?>
                                                                -
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if($monweek['area'] == 1): ?>
                                                                
                                                                cleaning
                                                            <?php else: ?>
                                                                
                                                                -
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo e($monweek['name']); ?></td>
                                                    </tr>
                                                    <?php
                                                        $noWeek++;
                                                    ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                    <tr>
                                                        <td colspan="7">Belum Ada Monitoring week</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        <?php else: ?>
                                            <thead class="table-light" align="center">
                                                <th style="vertical-align: middle;">Week</th>
                                                <th style="vertical-align: middle;">Condition</th>
                                                <th style="vertical-align: middle;">Voltage (V)</th>
                                                <th style="vertical-align: middle;">Ampere (A)</th>
                                                <th style="vertical-align: middle;">Auto Drain</th>
                                                <th style="vertical-align: middle;">Pre</th>
                                                <th style="vertical-align: middle;">After</th>
                                                <th style="vertical-align: middle;">Cleaning<br>Condensor</th>
                                                <th style="vertical-align: middle;">PIC</th>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    $noWeek = 1;
                                                ?>
                                                <?php $__currentLoopData = $weeksoy; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $monweek): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr align="center">
                                                        <td><?php echo e($noWeek); ?></td>
                                                        <td><?php echo e($monweek['condition']); ?></td>
                                                        <td><?php echo e($monweek['voltage']); ?></td>
                                                        <td><?php echo e($monweek['ampere']); ?></td>
                                                        <td><?php echo e($monweek['drain']); ?></td>
                                                        <td><?php echo e($monweek['pre']); ?></td>
                                                        <td><?php echo e($monweek['after']); ?></td>
                                                        <td>
                                                            <?php if($monweek['condensor'] == 1): ?>
                                                                
                                                                cleaning
                                                            <?php else: ?>
                                                                
                                                                -
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo e($monweek['name']); ?></td>
                                                    </tr>
                                                    <?php
                                                        $noWeek++;
                                                    ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        <?php endif; ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if($machine->unit->unit->unit == 'REFRIGERANT AIR DRYER'): ?>
                        <div class="monthly mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h4>Monthly Check</h4>
                                    <div class="table-responsive text-nowrap mb-4">
                                        <table class="table table-bordered">
                                            <thead class="table-light" align="center">
                                                <th style="vertical-align: middle;">Date</th>
                                                <th style="vertical-align: middle;">HP (High Pressure)</th>
                                                <th style="vertical-align: middle;">LP (Low Pressure)</th>
                                                <th style="vertical-align: middle;">Strainer</th>
                                            </thead>
                                            <tbody>
                                                <?php if($monthly): ?>
                                                    <tr>
                                                        <td><?php echo e(\Carbon\Carbon::parse($monthly->date)->format('d-m-Y')); ?>

                                                        </td>
                                                        <td><?php echo e($monthly->hp); ?></td>
                                                        <td><?php echo e($monthly->lp); ?></td>
                                                        <td><?php echo e($monthly->strainer); ?></td>
                                                    </tr>
                                                <?php else: ?>
                                                    <tr>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="issue mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h4>Issue & Recommendation</h4>
                                <div class="table-responsive text-nowrap">
                                    <table class="table table-bordered">
                                        <thead class="table-light" align="center">
                                            <th style="vertical-align: middle;">Date</th>
                                            <th  colspan="2" style="vertical-align: middle;">Issue</th>
                                            <th style="vertical-align: middle;">Recommendation</th>
                                            <th style="vertical-align: middle;">PN (Material)</th>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $no = 0;
                                            ?>
                                            <?php $__empty_1 = true; $__currentLoopData = $issue; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $issues): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <?php
                                                    $no++;
                                                ?>
                                                <tr>
                                                    <td><?php echo e($issues->date); ?></td>
                                                    <td  colspan="2">
                                                        <pre class="mb-1"
                                                            style="font-size: 15px; font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto;"><?php echo e($issues->issue); ?></pre>
                                                    </td>
                                                    <td>
                                                        <pre class="mb-1"
                                                            style="font-size: 15px; font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto;"><?php echo e($issues->recommendation); ?></pre>
                                                    </td>
                                                    <td><?php echo e($issues->pn); ?></td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <tr>
                                                    <td colspan="8" class="text-center">Belum Ada Issue</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="quote mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h4>Quotation</h4>
                                <div class="table-responsive text-nowrap mb-4">
                                    <table class="table table-bordered">
                                        <thead class="table-light" align="center">
                                            <tr>
                                                <th style="vertical-align: middle;">Date</th>
                                                <th style="vertical-align: middle;">No. Quote</th>
                                                <th style="vertical-align: middle;">No. PR</th>
                                                <th style="vertical-align: middle;">Title</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__empty_1 = true; $__currentLoopData = $quotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <tr>
                                                    <td><?php echo e(\Carbon\Carbon::parse($quote->estimated_date)->format('d-m-Y')); ?>

                                                    </td>
                                                    <td>
                                                        <a href="<?php echo e(route('quotation.show', $quote->id)); ?>"
                                                            class="text-black">
                                                            <?php echo e($quote->no_quote); ?>

                                                        </a>
                                                    </td>
                                                    <td><?php echo e($quote->no_pr); ?></td>
                                                    <td><?php echo e($quote->title); ?></td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <tr>
                                                    <td colspan="4" class="text-center">Belum Ada Quote
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mainlog mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h4>Maintenance Log</h4>
                                <div class="table-responsive text-nowrap">
                                    <table class="table table-bordered" align="center">
                                        <thead class="table-light" align="center">
                                            <th style="vertical-align: middle;">Date</th>
                                            <th style="vertical-align: middle;">Maintenance Description</th>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $no = 0;
                                            ?>
                                            <?php $__empty_1 = true; $__currentLoopData = $mainlog; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <?php
                                                    $no++;
                                                ?>
                                                <tr>
                                                    <td><?php echo e($item->date); ?></td>
                                                    <td>
                                                        <pre class="mb-1"
                                                            style="font-size: 15px; font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto;"><?php echo e($item->desc); ?></pre>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <tr>
                                                    <td colspan="3" class="text-center">Belum Ada Maintenance Log</td>
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
            <div class="content-backdrop fade"></div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>

    
    <?php echo $__env->yieldPushContent('before-script'); ?>

    <?php echo $__env->make('includes.sales.script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/moment/moment.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/main.js"></script>

    <script>
        $(document).on('click', '.view-quote', function(e) {
            e.preventDefault(); // Mencegah perubahan halaman segera

            var id = $(this).data('id');
            var idQ = $(this).data('quotation');
            var href = $(this).attr('href'); // Ambil URL tujuan

            $.ajax({
                url: '<?php echo e(url('quotation')); ?>/' + id + '/view_comment',
                type: 'POST',
                data: {
                    _token: '<?php echo e(csrf_token()); ?>', // Token CSRF
                },
                success: function(response) {
                    console.log(response); // Lakukan apa yang perlu dilakukan setelah AJAX sukses

                    // Arahkan ke halaman baru setelah AJAX selesai
                    window.location.href = href;
                },
                error: function(xhr) {
                    console.error("Error:", xhr.responseText); // Tangani error jika ada
                }
            });
        });
        $(document).on('click', '.view-quotation', function(e) {
            e.preventDefault(); // Mencegah perubahan halaman segera

            var id = $(this).data('id');
            var idQ = $(this).data('quotation');
            var href = $(this).attr('href'); // Ambil URL tujuan

            console.log(id);

            $.ajax({
                url: '<?php echo e(url('quotation')); ?>/' + id + '/view_comment',
                type: 'POST',
                data: {
                    _token: '<?php echo e(csrf_token()); ?>', // Token CSRF
                },
                success: function(response) {
                    console.log(response); // Lakukan apa yang perlu dilakukan setelah AJAX sukses

                    // Arahkan ke halaman baru setelah AJAX selesai
                    window.location.href = href;
                },
                error: function(xhr) {
                    console.error("Error:", xhr.responseText); // Tangani error jika ada
                }
            });
        });
        $(document).on('click', '.view-prospect', function(e) {
            e.preventDefault(); // Mencegah perubahan halaman segera

            var id = $(this).data('id');
            var idQ = $(this).data('quotation');
            var href = $(this).attr('href'); // Ambil URL tujuan    

            $.ajax({
                url: '<?php echo e(url('prospect')); ?>/' + id + '/view_comment',
                type: 'POST',
                data: {
                    _token: '<?php echo e(csrf_token()); ?>', // Token CSRF
                },
                success: function(response) {
                    console.log(response); // Lakukan apa yang perlu dilakukan setelah AJAX sukses

                    // Arahkan ke halaman baru setelah AJAX selesai
                    window.location.href = href;
                },
                error: function(xhr) {
                    console.error("Error:", xhr.responseText); // Tangani error jika ada
                }
            });
        });
    </script>

    <script src="<?php echo e(asset('assets')); ?>/js/tables-datatables-basic.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-monitoring-machine-visit.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-monitoring-dryer-visit.js"></script>

    <?php echo $__env->yieldPushContent('script'); ?>
</body>

</html>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/monitoring/visitor-change.blade.php ENDPATH**/ ?>