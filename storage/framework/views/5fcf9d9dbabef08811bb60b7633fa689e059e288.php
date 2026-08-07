<div class="modal animate__animated animate__fadeIn" id="detailReport<?php echo e($sale['id']); ?>" tabindex="-1"
    style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-2">
                        <img src="<?php echo e(url('') . '/' . $sale['image']); ?>" alt="" srcset=""
                            class="rounded-circle" style="width : 100%; height:100%;">
                    </div>
                    <div class="col-10">
                        <h4 class="badge bg-label-secondary w-100 text-center">Achievement</h4>
                        <h5 class="text-center">Rp <?php echo e(number_format($sale['total'], 0, ',', '.')); ?></h5>
                    </div>
                </div>
                <div class="row">
                    <ul class="p-0 m-0">
                        <?php
                            $bulanMap = [
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
                        <?php $__currentLoopData = $sale['jumlah']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <div class="avatar-initial bg-label-primary rounded">
                                        <div>
                                            <i
                                                class="mdi mdi-48px mdi-alpha-<?php echo e(strtolower(substr($bulanMap[$item['bulan']], 0, 1))); ?>-circle-outline"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <h6 class="mb-0"><?php echo e($bulanMap[$item['bulan']]); ?></h6>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="fw-semibold text-heading">Rp
                                            <?php echo e(number_format($item['total'], 0, ',', '.')); ?></span>
                                        <?php
                                            $persenanSales =
                                                $sale['target'] > 0
                                                    ? round(($item['total'] / $sale['target']) * 100, 2)
                                                    : 0;
                                            if ($persenanSales >= 100) {
                                                $label = 'success';
                                            } elseif ($persenanSales >= 90) {
                                                $label = 'warning';
                                            } else {
                                                $label = 'danger';
                                            }

                                        ?>
                                        <div class="ms-3 badge bg-label-<?php echo e($label); ?> rounded-pill">
                                            <?php echo e($persenanSales); ?>%</div>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/overview/report.blade.php ENDPATH**/ ?>