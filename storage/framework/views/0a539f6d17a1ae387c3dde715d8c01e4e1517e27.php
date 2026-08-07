
<?php $__env->startSection('title', 'Print Opname'); ?>
<div class="invoice-print p-4">
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="text-center">
            <h3>PT. REFTECH JAYA OPTIMA</h3>
            <h2 class="text-danger">Neraca (Standar)</h2>
            <h4>Dari <?php echo e($startString); ?> ke <?php echo e($endString); ?></h4>
        </div>
        <hr>
        <div class="mb-2">
            <?php
                $totalLancar = $piutang + $asset + $ppnMas;
                $totalTetap = $totalFixed - $grandTotalPenyusutan;
                $totalAktiva = $totalLancar + $totalTetap;
            ?>
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
                                Aktiva
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="lvl-1">
                                Aktiva Lancar
                            </span>
                        </td>
                    </tr>
                    <?php
                        $capPalembang = 425000000;
                        $modPalembang = 575000000;
                    ?>
                    <tr>
                        <td>
                            <span class="lvl-2">
                                Bank
                            </span>
                        </td>
                        <td class="fw-medium"><?php echo e(number_format($bank->saldo, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                BCA IDR
                            </span>
                        </td>
                        <td><?php echo e(number_format($bank->saldo, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                Capital Cabang Palembang
                            </span>
                        </td>
                        <td><?php echo e(number_format($capPalembang, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                Modal Cabang Palembang
                            </span>
                        </td>
                        <td><?php echo e(number_format($modPalembang, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Jumlah Kas dan Bank
                            </span>
                        </td>
                        <td class="fw-medium border-top"><?php echo e(number_format($bank->saldo + $capPalembang + $modPalembang, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="lvl-1">
                                Piutang Dagang
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Piutang Usaha
                            </span>
                        </td>
                        <td class="fw-medium"><?php echo e(number_format($piutang, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                Piutang Usaha
                            </span>
                        </td>
                        <td><?php echo e(number_format($piutang, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Jumlah Piutang Dagang
                            </span>
                        </td>
                        <td class="fw-medium border-top"><?php echo e(number_format($piutang, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="lvl-1">
                                Persediaan
                            </span>

                        </td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Persediaan Barang Dagang
                            </span>
                        </td>
                        <td class="fw-medium"><?php echo e(number_format($asset, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                Persediaan Barang Dagang
                            </span>
                        </td>
                        <td><?php echo e(number_format($asset, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Jumlah Persediaan
                            </span>
                        </td>
                        <td class="fw-medium border-top"><?php echo e(number_format($asset, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="lvl-1">
                                Aktiva Lancar Lainnya
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                PPN Masukan
                            </span>
                        </td>
                        <td class="fw-medium"><?php echo e(number_format($ppnMas, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Jumlah Aktiva Lancar Lainnya
                            </span>
                        </td>
                        <td class="fw-medium border-top"><?php echo e(number_format($ppnMas, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Jumlah Aktiva Lancar
                            </span>
                        </td>
                        <td class="fw-medium border-top"><?php echo e(number_format($totalLancar, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="lvl-0">
                                Aktiva Tetap
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="lvl-1">
                                Nilai Histori
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Aset Tetap
                            </span>
                        </td>
                        <td class="fw-medium border-top"><?php echo e(number_format($totalFixed, 0, ',', '.')); ?></td>
                    </tr>
                    <?php $__currentLoopData = $fixedAsset; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <span class="lvl-3">
                                    <?php echo e($item->type); ?>

                                </span>
                            </td>
                            <td><?php echo e(number_format($item->total_amount, 0, ',', '.')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Jumlah Nilai Histori
                            </span>
                        </td>
                        <td class="fw-medium border-top"><?php echo e(number_format($totalFixed ?? 0, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="lvl-1">
                                Akumulasi Penyusutan
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Akumulasi Penyusutan
                            </span>
                        </td>
                        <td class="fw-medium border-top"><?php echo e(number_format($grandTotalPenyusutan, 0, ',', '.')); ?></td>
                    </tr>
                    <?php $__currentLoopData = $penyusutan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <span class="lvl-3">
                                    Akum. Penys. <?php echo e($item['type']); ?>

                                </span>
                            </td>
                            <td class="text-danger"> - <?php echo e(number_format($item['total_penyusutan'], 0, ',', '.')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Jumlah Akumulasi Penyusutan
                            </span>
                        </td>
                        <td class="text-danger border-top"><?php echo e(number_format($grandTotalPenyusutan ?? 0, 0, ',', '.')); ?>

                        </td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Jumlah Aktiva Tetap
                            </span>
                        </td>
                        <td class="fw-medium border-top"><?php echo e(number_format($totalTetap ?? 0, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="lvl-1">
                                OTHER ASSETS
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Jumlah OTHER ASSETS
                            </span>
                        </td>
                        <td class="fw-medium border-top"><?php echo e(number_format(0, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Jumlah Aktiva
                            </span>
                        </td>
                        <td class="fw-medium border-top"><?php echo e(number_format($totalAktiva, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="lvl-1">
                                Kewajiban dan Ekuitas
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="lvl-1">
                                Kewajiban
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="lvl-1">
                                Kewajiban Lancar
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="lvl-1">
                                Hutang Dagang
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Jumlah Hutang Dagang
                            </span>
                        </td>
                        <td class="fw-medium border-top"><?php echo e(number_format(0, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="fw-medium">
                            <span class="lvl-1">
                                Kewajiban Lancar Lain
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                PPN Keluaran
                            </span>
                        </td>
                        <td><?php echo e(number_format($ppnKel, 0, ',', '.')); ?></td>
                    </tr>
                    <td class="fw-medium">
                        <span class="lvl-2">
                            Jumlah Kewajiban Lancar Lain
                        </span>
                    </td>
                    <td class="fw-medium border-top"><?php echo e(number_format($ppnKel ?? 0, 0, ',', '.')); ?></td>
                    </tr>
                    </tr>
                    <td class="fw-medium">
                        <span class="lvl-2">
                            Jumlah Kewajiban Lancar
                        </span>
                    </td>
                    <td class="fw-medium border-top"><?php echo e(number_format($ppnKel ?? 0, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="fw-medium">
                            <span class="lvl-1">
                                Kewajiban Jangka Panjang
                            </span>
                        </td>
                    </tr>
                    </tr>
                    <td class="fw-medium">
                        <span class="lvl-2">
                            Jumlah Kewajiban Jangka Panjang
                        </span>
                    </td>
                    <td class="fw-medium border-top"><?php echo e(number_format(0, 0, ',', '.')); ?></td>
                    </tr>
                    </tr>
                    <td class="fw-medium">
                        <span class="lvl-2">
                            Jumlah Kewajiban
                        </span>
                    </td>
                    <td class="fw-medium border-top"><?php echo e(number_format($ppnKel ?? 0, 0, ',', '.')); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="fw-medium">
                            <span class="lvl-1">
                                Ekuitas
                            </span>
                        </td>
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
                        $ekujiban = $totalekuitas + $ppnKel;
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
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Jumlah Ekuitas Dan Kewajiban
                            </span>
                        </td>
                        <td class="fw-medium border-top"><?php echo e(number_format($ekujiban ?? 0, 0, ',', '.')); ?></td>
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
    <style>
    </style>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/app-invoice-print.js"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/finance/balance/print.blade.php ENDPATH**/ ?>