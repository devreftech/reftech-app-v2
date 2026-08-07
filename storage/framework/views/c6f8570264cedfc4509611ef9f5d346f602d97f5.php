
<?php $__env->startSection('title', 'Print Opname'); ?>
<div class="invoice-print p-4">
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="text-center">
            <h3>PT. REFTECH JAYA OPTIMA</h3>
            <h2 class="text-danger">Laba/Rugi (Standar)</h2>
            <h4>Dari <?php echo e($startString); ?> ke <?php echo e($endString); ?></h4>
        </div>
        <hr>
        
        <?php
            $incomeCharge = $incomeSum - $chargeSum;
            $subtotal = $poSum - $modalSum;
            $total = $subtotal - $expenseSum + $incomeCharge;
        ?>
        <div class="mb-2">
            <table class="table table-borderless m-0" style="width: 100%">
                <thead class="table-light border-top">
                    <tr>
                        <th>Description</th>
                        <th><?php echo e($startStringYear); ?>-<?php echo e($endString); ?></th>
                    </tr>
                </thead>
                <tbody class="text-dark">
                    <tr>
                        <td colspan="2" class="fw-medium">Pendapatan</td>
                    </tr>
                    <tr>
                        <td class="fw-medium">Pendapatan</td>
                        <td class="fw-medium"><?php echo e(number_format($poSum, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td>Penjualan</td>
                        <td><?php echo e(number_format($poSum, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td>Potongan Penjualan</td>
                        <td class="text-danger">- 0
                            
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-medium">Jumlah Pendapatan</td>
                        <td class="fw-medium border-top"><?php echo e(number_format($poSum, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="fw-medium">Harga Pokok Penjualan</td>
                    </tr>
                    <tr>
                        <td class="fw-medium">Harga Pokok Penjualan</td>
                        <td class="fw-medium"><?php echo e(number_format($modalSum, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td>Harga Barang Dagang</td>
                        <td><?php echo e(number_format($modalSum, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td class="fw-medium">Jumlah Harga Pokok Penjualan</td>
                        <td class="fw-medium border-top"><?php echo e(number_format($modalSum, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold">LABA KOTOR</td>
                        <td class="fw-bold border-top"><?php echo e(number_format($poSum, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="fw-medium">Beban Operasi</td>
                    </tr>
                    <?php $__currentLoopData = $allExpense; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($item->account->name); ?></td>
                            <td><?php echo e(number_format($item->amount, 0, ',', '.')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="fw-medium">Jumlah Beban Operasi</td>
                        <td class="fw-medium border-top"><?php echo e(number_format($expenseSum ?? 0, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td class="fw-medium">PENDAPATAN OPERASI</td>
                        <td class="fw-medium border-top"><?php echo e(number_format($subtotal - $expenseSum, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="fw-medium">Pendapatan Beban Lain</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="fw-medium">Pendapatan Lain Lain</td>
                    </tr>
                    <?php $__currentLoopData = $allIncome; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($item->description); ?></td>
                            <td><?php echo e(number_format($item->amount, 0, ',', '.')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="fw-medium">Jumlah Pendapatan Lain</td>
                        <td class="fw-medium border-top"><?php echo e(number_format($incomeSum ?? 0, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="fw-medium">Beban Lain Lain</td>
                    </tr>
                    <?php $__currentLoopData = $allCharge; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($item->description); ?></td>
                            <td><?php echo e(number_format($item->amount, 0, ',', '.')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="fw-medium">Jumlah Beban Lain</td>
                        <td class="fw-medium border-top"><?php echo e(number_format($chargeSum ?? 0, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td class="fw-medium">Jumlah Pendapatan Beban Lain</td>
                        <td class="fw-medium border-top"><?php echo e(number_format($incomeCharge ?? 0, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td class="fw-medium">LABA(RUGI) BERSIH</td>
                        <td class="fw-medium border-top"><?php echo e(number_format($total ?? 0, 0, ',', '.')); ?></td>
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

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/finance/fixed/print.blade.php ENDPATH**/ ?>