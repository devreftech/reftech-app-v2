
<?php $__env->startSection('title', 'All Machine'); ?>

<div class="invoice-print p-4">
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
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
            <div class="text-end">
                <h3 class="fw-bold">Recap Monthly All Machine</h3>
            </div>
        </div>
        <hr class="my-2">

        <?php $__currentLoopData = $result; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="d-flex justify-content-between mb-2">
                <h5><?php echo e($item['machine']); ?></h5>
                <h5><?php echo e($item['plant']); ?></h5>
            </div>

            <div class="table-responsive text-nowrap">
                <?php if($item['unit'] != 'REFRIGERANT AIR DRYER'): ?>
                    <table class="table table-bordered">
                        <thead class="table-light" align="center">
                            <th style="vertical-align: middle;">Date</th>
                            <th style="vertical-align: middle;">Condition</th>
                            <th style="vertical-align: middle;">Running<br>Hour</th>
                            <th style="vertical-align: middle;">Load<br>Hour</th>
                            <th style="vertical-align: middle;">Press.</th>
                            <th style="vertical-align: middle;">Temp.<br>(85°C - 95°C)</th>
                            <th style="vertical-align: middle;">Oil<br>Level</th>
                            <th style="vertical-align: middle;">Kebocoran</th>
                            <th style="vertical-align: middle;">PIC</th>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $item['daily']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $daily): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($daily['date']); ?></td>
                                    <td><?php echo e($daily['condition']); ?></td>
                                    <td><?php echo e($daily['running']); ?></td>
                                    <td><?php echo e($daily['loading']); ?></td>
                                    <td><?php echo e($daily['pressure']); ?></td>
                                    <td>
                                        <?php
                                            $stringTemp = $daily['temp'] ?? ''; // Pastikan nilai tidak null
                                            $tempNumber = null;

                                            if (preg_match('/\d+(\.\d+)?/', $stringTemp, $matches)) {
                                                $tempNumber = (float) $matches[0]; // Gunakan float agar mendukung desimal
                                            }
                                        ?>

                                        <?php if(!is_null($tempNumber) && $tempNumber > 94): ?>
                                            <p class="mb-0 fw-bold fs-6 text-danger">
                                                <?php echo e($daily['temp']); ?></p>
                                        <?php else: ?>
                                            <?php echo e($daily['temp']); ?>

                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($daily['oil_level']); ?></td>
                                    <td><?php echo e($daily['leak']); ?></td>
                                    <td><?php echo e($daily['pic']); ?></td>
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
                            <th style="vertical-align: middle;">Dew P.</th>
                            <th style="vertical-align: middle;">Auto<br>Drain</th>
                            <th style="vertical-align: middle;">Fan<br>Kondenser</th>
                            <th style="vertical-align: middle;">Kebocoran</th>
                            <th style="vertical-align: middle;">PIC</th>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $item['daily']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $daily): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($daily['date']); ?></td>
                                    <td><?php echo e($daily['condition']); ?></td>
                                    <td><?php echo e($daily['temp']); ?></td>
                                    <td><?php echo e($daily['temp_out']); ?></td>
                                    <td><?php echo e($daily['dew']); ?></td>
                                    <td><?php echo e($daily['drain']); ?></td>
                                    <td><?php echo e($daily['fan']); ?></td>
                                    <td><?php echo e($daily['leak']); ?></td>
                                    <td><?php echo e($daily['pic']); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="issue mt-5">
                <h5>Issue Recommendation</h5>
                <div class="table-responsive text-nowrap mb-4">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th style="width:20%;">Date</th>
                                <th>Issue</th>
                                <th style="width:25%;">Pic</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $item['log']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $issues): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($issues['date'] ?? 'N/A'); ?></td>
                                    <td>
                                        <pre class="mb-1"
                                            style="font-size: 15px; font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto;"><?php echo e($issues['log']); ?></pre>
                                    </td>
                                    <td><?php echo e($issues['pic']); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="3"> Belum Ada Issue Recomendation</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mainlog mt-5">
                <h5>Maintenance Log</h5>
                <div class="table-responsive text-nowrap mb-4">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th style="width:20%;">Date</th>
                                <th>Maintenance</th>
                                <th style="width:25%;">Pic</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $item['mainlog']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mainlog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($mainlog['date'] ?? 'N/A'); ?></td>
                                    <td>
                                        <pre class="mb-1"
                                            style="font-size: 15px; font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto;"><?php echo e($mainlog['log']); ?></pre>
                                    </td>
                                    <td><?php echo e($mainlog['technician']); ?></td>
                                    
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="3"> Belum Ada Maintenance Log</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="weekly mb-5">
                <h5>Weekly Monitoring</h5>

                <div class="table-responsive text-nowrap mb-4">
                    <table class="table table-bordered">
                        <?php if($item['unit'] == 'REFRIGERANT AIR DRYER'): ?>
                            <thead class="table-light">
                                <th style="vertical-align: middle;">Week</th>
                                <th style="vertical-align: middle;">Condition</th>
                                <th style="vertical-align: middle;">Vibration</th>
                                <th style="vertical-align: middle;">Voltage</th>
                                <th style="vertical-align: middle;">Ampere L</th>
                                <th style="vertical-align: middle;">Cleaning Cooler</th>
                                <th style="vertical-align: middle;">Cek Coupling / Belt</th>
                                <th style="vertical-align: middle;">Cleaning Compressor & Area</th>
                                <th style="vertical-align: middle;">PIC</th>
                            </thead>
                            <tbody>
                                <?php
                                    $noWeek = 1;
                                ?>
                                <?php $__empty_1 = true; $__currentLoopData = $item['weekly']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $monweek): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
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
                                            <?php else: ?>
                                                
                                                Not Ok
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
                            <thead class="table-light">
                                <th>Week</th>
                                <th>Condition</th>
                                <th>Voltage</th>
                                <th>Ampere</th>
                                <th>Auto Drain</th>
                                <th>Pre</th>
                                <th>After</th>
                                <th>Condensor</th>
                                <th>PIC</th>
                            </thead>
                            <tbody>
                                <?php
                                    $noWeek = 1;
                                ?>
                                <?php $__currentLoopData = $item['weekly']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $monweek): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
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

            <?php if($item['unit'] == 'REFRIGERANT AIR DRYER'): ?>
                <div class="monthly mb-5">
                    <h5>Monthly Monitoring</h5>
                    <div class="table-responsive text-nowrap mb-4">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <thead class="table-light">
                                    <th>Date</th>
                                    <th>Condition</th>
                                    <th>HP (High Pressure)</th>
                                    <th>LP (Low Pressure)</th>
                                    <th>Strainer</th>
                                    <th>PIC</th>
                                </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $item['monthly']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $monweek): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($monweek['date'] ?? '-'); ?></td>
                                        <td><?php echo e($monweek['condition'] ?? '-'); ?></td>
                                        <td><?php echo e($monweek['hp'] ?? '-'); ?></td>
                                        <td><?php echo e($monweek['lp'] ?? '-'); ?></td>
                                        <td><?php echo e($monweek['strainer'] ?? '-'); ?></td>
                                        <td><?php echo e($monweek['pic'] ?? '-'); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7">Belum Ada Monitoring week</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <table class="no-break">
                <tr>
                    <td class="text-center">
                        <p>PT Reftech Jaya Optima</p>
                        <div class="">
                            <img src="<?php echo e(url('') . '/asset/sign/ttdAngel.jpg'); ?>" alt="" srcset=""
                                height="100">
                        </div>
                        <p>Angel Irene</p>
                    </td>
                    <td style="width: 50%"></td> <!-- Kolom kosong untuk jarak tengah -->
                    <td class="text-center">
                        <p>PT Fajar Surya Wisesa</p>
                        <div class="pb-5"></div>
                        <p class="pt-3">..........................................</p>
                    </td>
                </tr>
            </table>

            <div class="invoice mb-4">
                <?php $__currentLoopData = $item['reports']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="page-break"></div>
                    <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                        <div class="mb-xl-0 pb-1">
                            <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                                <span class="app-brand-logo demo">
                                    <span style="color: var(--bs-primary)">
                                        <img class="text-md"
                                            src="<?php echo e(url('https://reftech.id/wp-content/uploads/2021/10/Reftech-Logo-Hitam.png')); ?>"
                                            alt="" srcset="" class="img-logo">
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
                        <div class="text-end">
                            <h3 class="fw-bold">SERVICE REPORT</h3>
                            <div>
                                <span class="fw-bolder">#<?php echo e($service['no_service']); ?></span>
                            </div>
                            <div class="mt-1">
                                <span class="text-muted"><?php echo e($service['date']); ?></span>
                            </div>
                            <div class="mt-1">
                                <?php
                                    $badgeClass = '';
                                    $label = $service['type'];

                                    switch ($service['type']) {
                                        case 'Visit':
                                            $badgeClass = 'success';
                                            break;
                                        case 'Service':
                                            $badgeClass = 'danger';
                                            break;
                                        case 'General':
                                            $badgeClass = 'primary';
                                            $label = 'General Check';
                                            break;
                                        default:
                                            $badgeClass = '';
                                            break;
                                    }
                                ?>
                                <span
                                    class="badge fs-6 rounded-pill bg-label-<?php echo e($badgeClass); ?>"><?php echo e($label); ?></span>
                            </div>
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="row mb-3">
                        <div class="col-2 fw-medium">
                            <p class="mb-1">Customers </p>
                            <p class="mb-1">Address </p>
                            <p class="mb-1">PIC </p>
                        </div>
                        <div class="col-4">
                            <p class="mb-1">: <?php echo e($service['company']); ?></p>
                            <p class="mb-1">: <?php echo e($service['area']); ?></p>
                            <p class="mb-1">: <?php echo e($service['pic']); ?></p>
                        </div>
                        <div class="col-2 fw-medium">
                            <p class="mb-1">Unit Type </p>
                            <p class="mb-1">Serial Number </p>
                            <p class="mb-1">Running & Load </p>
                        </div>
                        <div class="col-4">
                            <p class="mb-1">: <?php echo e($service['unit']); ?></p>
                            <p class="mb-1">:
                                <?php echo e($service['tag']); ?> |
                                <?php echo e($service['location']); ?>

                            </p>
                            <p class="mb-1">: <?php echo e($service['running']); ?> | <?php echo e($service['load']); ?></p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-2 fw-medium">
                            <p class="mb-1">Job Description </p>
                        </div>
                        <div class="col-10 d-flex gap-1">
                            <p>: </p>
                            <p class="mb-1">: <?php echo e($service['jobdesc']); ?></p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <h5 class="my-2">Description</h5>
                            <pre class="mb-1"
                                style="font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;"><?php echo e($service['desc']); ?>

                </pre>
                        </div>
                        <div class="col-6">
                            <h5 class="my-2">Recomendation</h5>
                            <pre class="mb-1"
                                style="font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;"><?php echo e($service['recomendation']); ?></pre>
                        </div>
                    </div>
                    <hr>
                    <h5 class="my-4">Picture</h5>
                    <div class="row mb-5">
                        <?php $__currentLoopData = $service['picture']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $picture): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-4 text-center">
                                <img src="<?php echo e(url('') . '/' . $picture->picture); ?>" alt="" srcset=""
                                    style="max-width : 200px;">
                                <p><?php echo e($picture->keterangan); ?></p>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <div class="row mt-5">
                        <div class="col-4 mt-5 text-center">
                            <p>PT Reftech Jaya Optima</p>
                            <?php if(isset($service['technician_sign'])): ?>
                                <img src="<?php echo e(url('') . '/' . $service['technician_sign']); ?>" alt=""
                                    srcset="" height="100">
                            <?php else: ?>
                                <div class="pb-5"></div>
                            <?php endif; ?>
                            <p class="pt-3">( <?php echo e($service['technician']); ?> )</p>
                        </div>
                        <div class="col-4"></div>
                        <div class="col-4 mt-5 text-center">
                            <p class=""><?php echo e($service['company']); ?></p>
                            <?php if(isset($service['sign_client'])): ?>
                                <img src="<?php echo e(url('') . '/' . $service['sign_client']); ?>" alt=""
                                    srcset="" height="100">
                            <?php else: ?>
                                <div class="pb-5"></div>
                            <?php endif; ?>
                            <p class="pt-3">( <?php echo e($service['pic']); ?> )</p>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="page-break"></div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php $__env->startPush('after-style'); ?>
    <!-- Page CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/css/pages/app-monitoring-print.css" />
    <link rel="stylesheet" href="style.css">
<?php $__env->stopPush(); ?>
<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/app-invoice-print.js"></script>
    <script>
        window.addEventListener('beforeprint', () => {
            const rows = document.querySelectorAll('table tr');
            rows.forEach((row, index) => {
                const rect = row.getBoundingClientRect();
                if (rect.top > window.innerHeight) {
                    row.style.marginTop = '20mm';
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/monitoring/allMachine.blade.php ENDPATH**/ ?>