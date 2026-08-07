
<?php $__env->startSection('title', 'Print Opname'); ?>
<div class="invoice-print p-4">
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="text-center">
            <h3>PT. REFTECH JAYA OPTIMA</h3>
            <h2 class="text-danger">Perubahan Modal (Standar)</h2>
            <h4>Dari <?php echo e($startString); ?> ke <?php echo e($endString); ?></h4>
        </div>
        <hr>
        <div class="mb-2">
            <table class="table table-borderless m-0" style="width: 100%">
                <thead class="table-light border-top">
                    <tr>
                        <th>Description</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tbody class="text-dark">
                    <tr>
                        <td colspan="2" class="fw-medium">Ekuitas</td>
                    </tr>
                    <?php
                        if (@$month) {
                            $ekuitas = 250000000 + $labaTahunTahun - $prive - $labaBulanIni;
                            $totalekuitas = $ekuitas + $labaBulanIni;
                            $sebelumnya = $labaTahunTahun - $labaBulanIni;
                        } else {
                            $ekuitas = 250000000 + $labaTahunTahun - $prive - $labaTahunIni;
                            $totalekuitas = $ekuitas + $labaTahunIni;
                            $sebelumnya = $labaTahunTahun - $labaTahunIni;
                        }
                    ?>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Ekuitas
                            </span>
                        </td>
                        <td class="fw-medium"><?php echo e(number_format($ekuitas, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                Modal
                            </span>
                        </td>
                        <td><?php echo e(number_format(250000000, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                Laba Ditahan
                            </span>
                        </td>
                        <td><?php echo e(number_format($labaTahunLalu, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                Prive
                            </span>
                        </td>
                        <td class="text-danger">- <?php echo e(number_format($prive, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                Laba Tahun Sebelumnya
                            </span>
                        </td>
                        <td><?php echo e(number_format($sebelumnya, 0, ',', '.')); ?></td>
                        
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                OPENING BALANCE EQUITY
                            </span>
                        </td>
                        <td><?php echo e(number_format(0, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                Laba <?php echo e(@$month ? 'Bulan' : 'Tahun'); ?> Ini
                            </span>
                        </td>
                        <td><?php echo e(number_format(@$month ? $labaBulanIni : $labaTahunIni, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Jumlah Ekuitas
                            </span>
                        </td>
                        <td class="fw-medium border-top"><?php echo e(number_format($totalekuitas ?? 0, 0, ',', '.')); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->startPush('after-style'); ?>
    <!-- Page CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/css/pages/app-invoice-print-income.css" />
    <link rel="stylesheet" href="style.css">
<?php $__env->stopPush(); ?>
<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/app-invoice-print.js"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/finance/equity/print.blade.php ENDPATH**/ ?>