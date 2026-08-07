
<?php $__env->startSection('title', 'Service reports'); ?>

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
                <h3 class="fw-bold">DAILY MONITORING</h3>
                <div>
                    <span class="fw-bolder"><?php echo e($machine->unit->unit->unit); ?></span>
                </div>
                <div class="mt-1">
                    <span class="text-muted"><?php echo e($machine->unit->brand); ?> <?php echo e($machine->unit->unit->sku); ?></span>
                </div>
                <div class="mt-1">
                    <span class="text-muted"><?php echo e($machine->tag); ?> - <?php echo e($machine->location); ?></span>
                </div>
            </div>
        </div>
        <hr class="my-2">
        

        <h5>Daily Check</h5>
        <div class="table-responsive text-nowrap">
            <?php if($machine->unit->unit->unit != 'REFRIGERANT AIR DRYER'): ?>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <th>Date</th>
                        <th>Condition</th>
                        <th>R Hr.</th>
                        <th>L Hr.</th>
                        <th>Press.</th>
                        <th>Temp. (90°C - 100°C)</th>
                        <th>Oil Lvl</th>
                        <th>Kebocoran</th>
                        <th>PIC</th>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $compressor; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($item['date']); ?></td>
                                <td><?php echo e($item['condition']); ?></td>
                                <td><?php echo e($item['running']); ?></td>
                                <td><?php echo e($item['loading']); ?></td>
                                <td><?php echo e($item['pressure']); ?></td>
                                <td>
                                    <?php
                                        $stringTemp = $item['temp'] ?? '';
                                        $stringTemp = str_replace(',', '.', $stringTemp); // ganti koma jadi titik

                                        $tempNumber = null;

                                        if (preg_match('/\d+(\.\d+)?/', $stringTemp, $matches)) {
                                            $tempNumber = (float) $matches[0];
                                        }
                                    ?>

                                    <?php if(!is_null($tempNumber) && $tempNumber >= 100): ?>
                                        <p class="mb-0 fw-bold fs-6 text-danger">
                                            <?php echo e($item['temp']); ?>

                                        </p>
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
                    <thead class="table-light">
                        <th>Date</th>
                        <th>Condition</th>
                        <th>Temp IN</th>
                        <th>Temp OUT</th>
                        <th>Dew P.</th>
                        <th>Auto Drain</th>
                        <th>Fan Kondenser</th>
                        <th>Kebocoran</th>
                        <th>PIC</th>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $dryer; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($item['date']); ?></td>
                                <td><?php echo e($item['condition']); ?></td>
                                <td><?php echo e($item['temp']); ?></td>
                                <td><?php echo e($item['temp_out']); ?></td>
                                <td>
                                    <?php if(!is_null($item['dew']) && $item['dew'] > 10): ?>
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

        <?php if($machine->unit->unit->unit == 'REFRIGERANT AIR DRYER'): ?>
            <div class="card invoice-preview-card mb-4">
                <div class="card-body">
                    <div class="monthly mb-4">
                        <h5>Monthly Check</h5>
                        <div class="table-responsive text-nowrap mb-4">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <th>Date</th>
                                    <th>HP (High Pressure)</th>
                                    <th>LP (Low Pressure)</th>
                                    <th>Strainer</th>
                                </thead>
                                <tbody>
                                    <?php if($monthly): ?>
                                        <tr>
                                            <td><?php echo e(\Carbon\Carbon::parse($monthly->date)->format('d-m-Y')); ?></td>
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

        <div class="issue mt-5">
            <h5>Issue & Recommendation</h5>
            <div class="table-responsive text-nowrap mb-4">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th style="width:10%;">Date</th>
                            <th>Issue</th>
                            <th>Recommendation</th>
                            <th>Part Number</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $issue; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $issues): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($issues->date ?? 'N/A'); ?></td>
                                <td>
                                    <pre class="mb-1"
                                        style="font-size: 15px; font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto;"><?php echo e($issues->issue); ?></pre>
                                </td>
                                <td><?php echo e($issues->recommendation ?? '-'); ?></td>
                                <td><?php echo e($issues->pn ?? '-'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                        <?php $__currentLoopData = $mainlog; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($item->date ?? 'N/A'); ?></td>
                                <td>
                                    <pre class="mb-1"
                                        style="font-size: 15px; font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto;"><?php echo e($item->desc); ?></pre>
                                </td>
                                <td><?php echo e($item->name); ?></td>
                                
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
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

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/monitoring/service-visitor-print-prokemas.blade.php ENDPATH**/ ?>