
<?php $__env->startSection('title', 'Print Opname'); ?>
<div class="invoice-print p-4">
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="text-center">
            <h3>PT. REFTECH JAYA OPTIMA</h3>
            <h2 class="text-danger">Arus Kas (Metode Langsung)</h2>
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
                        <td colspan="2">
                            <span class="lvl-0">
                                Arus Kas dari Aktivitas Operasi
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-1">
                                Kas dari Penjualan
                            </span>
                        </td>
                        <td><?php echo e(number_format($quotation, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Pendapatan Lain Lain
                            </span>
                        </td>
                        <td class="fw-medium"><?php echo e(number_format($income, 0, ',', '.')); ?></td>
                    </tr>
                    <?php $__currentLoopData = $pendapatan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <span class="lvl-3">
                                    <?php echo e($item->description); ?>

                                </span>
                            </td>
                            <td><?php echo e(number_format($item->amount, 0, ',', '.')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <span class="lvl-1">
                                Kas Untuk Pembelian
                            </span>
                        </td>
                        <td>TBA</td>
                    </tr>
                    <?php $__currentLoopData = $expensePerAccount; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <span class="lvl-3">
                                    <?php echo e($item->name); ?>

                                </span>
                            </td>
                            <td class="text-danger">-<?php echo e(number_format($item->total_amount, 0, ',', '.')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <span class="lvl-1">
                                Biaya Lain Lain
                            </span>
                        </td>
                        <td>TBA</td>
                    </tr>
                    <?php $__currentLoopData = $biaya; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <span class="lvl-3">
                                    <?php echo e($item->description); ?>

                                </span>
                            </td>
                            <td class="text-danger">-<?php echo e(number_format($item->amount, 0, ',', '.')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <span class="lvl-1">
                                Laba/Rugi Penghentian Aktiva Tetap
                            </span>
                        </td>
                        <td>TBA</td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Laba(Rugi) Operasi sebelum berubah di Operasi Aktiva dan Kewajiban
                            </span>
                        </td>
                        <td class="fw-medium border-top">TBA</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="lvl-1">
                                Berkurang(Bertambah) pada Operasi Aktiva
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Persediaan Barang Dagang
                            </span>
                        </td>
                        <td class="fw-medium border-top">TBA</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                Screw Compressor 132 KW
                            </span>
                        </td>
                        <td>TBA</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                Piutang Lain-lain IDR
                            </span>
                        </td>
                        <td><?php echo e(number_format($piutang, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                PPN Masukan
                            </span>
                        </td>
                        <td><?php echo e(number_format($ppnMas, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                Transakasi Aktiva Tetap
                            </span>
                        </td>
                        <td class="text-danger">TBA</td>
                    </tr>
                    <tr>
                        <td class="fw-bolder">
                            <span class="lvl-2">
                                Akumulasi Penyusutan
                            </span>
                        </td>
                        <td class="text-danger">TBA</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-2">
                                Akum. Peny. Mesin
                            </span>
                        </td>
                        <td class="text-danger">TBA</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-1">
                                Jumlah Berkurang(Bertambah) pada Operasi Aktiva
                            </span>
                        </td>
                        <td class="text-danger border-top">TBA</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-1">
                                Bertambah (berkurang) pada Operasi Kewajiban
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-2">
                                PPN Keluaran
                            </span>
                        </td>
                        <td class="border-top">-<?php echo e(number_format($ppnKel, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-1">
                                Jumlah Bertambah (berkurang) pada Operasi Kewajiban
                            </span>
                        </td>
                        <td class="text-danger border-top">-<?php echo e(number_format($ppnKel, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-1">
                                Kas bersih (dipakai)/ dihasilkan oleh Aktivitas Operasi
                            </span>
                        </td>
                        <td class="border-top">TBA</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="lvl-0">
                                Arus Kas dari Aktivitas Investasi
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-1">
                                Akumulasi Penyusutan
                            </span>
                        </td>
                        <td class="text-danger">TBA</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                Peralatan Kantor
                            </span>
                        </td>
                        <td>TBA</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                Transaksi Aktiva Tetap
                            </span>
                        </td>
                        <td class="text-danger">TBA</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                Tools
                            </span>
                        </td>
                        <td class="text-danger">TBA</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                Mesin Compressor
                            </span>
                        </td>
                        <td>TBA</td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Kas bersih yg dihasilkan / (dipakai) oleh Aktivitas Investasi
                            </span>
                        </td>
                        <td class="fw-medium border-top">TBA</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="fw-bold fs-4">Arus Kas dari Aktivitas Pendanaan</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="fw-bolder fs-5">Ekuitas</td>
                    </tr>
                    <tr>
                        <td>Dividen</td>
                        <td class="text-danger">- <?php echo e(number_format($prive, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td class="fw-medium"> Kas bersih yg dihasilkan dari / (dipakai) oleh Aktivitas Pendanaan</td>
                        <td class="fw-medium border-top text-danger">-TBA</td>
                    </tr>
                    <tr>
                        <td class="fw-medium"> Kas bersih dihasilkan oleh / (dipakai) di Period ini</td>
                        <td class="fw-medium border-top">TBA</td>
                    </tr>
                    <tr>
                        <td class="fw-medium"> Kas & Setara Kas pada Awal Periode</td>
                        <td class="fw-medium border-top">TBA</td>
                    </tr>
                    <tr>
                        <td class="fw-medium"> Kas & Setara Kas pada Akhir Periode</td>
                        <td class="fw-medium border-top">TBA</td>
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

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/finance/cashflow/print.blade.php ENDPATH**/ ?>