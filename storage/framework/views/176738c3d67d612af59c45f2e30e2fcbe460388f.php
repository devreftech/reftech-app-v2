
<?php $__env->startSection('reports'); ?>
    <div class="invoice-print p-4">
        <div class="contianter">
            <div class="row mb-3">
                <div class="col-6" style="background-color: yellow;">
                    <div class="row">
                        <div class="col-6 text-center">
                            <h4 class="my-1 text-black">
                                MONITORING :
                            </h4>
                        </div>
                        <div class="col-6 text-center">
                            <h4 class="my-1 text-black">
                                <?php echo e(strtoupper($month)); ?>

                            </h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <table class="table table-bordered m-0" style="width: 100%">
                    <thead class="table-light border-top">
                        <tr class="title">
                            <th colspan="5" class="text-center title" style="background-color: pink">Summary Pekerjaan Service</th>
                        </tr>
                        <tr class="subtitle">
                            <th>Date</th>
                            <th>Location Unit</th>
                            <th>Tag</th>
                            <th>Model / type</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $statusMon; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($item->date); ?></td>
                                <td><?php echo e($item->location); ?></td>
                                <td><?php echo e($item->tag); ?></td>
                                <td><?php echo e($item->machine); ?></td>
                                <td><?php echo e($item->desc); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php $__env->startPush('after-style'); ?>
        <!-- Page CSS -->
        <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/css/pages/app-reports-print-landscape.css" />
        <link rel="stylesheet" href="style.css">
    <?php $__env->stopPush(); ?>
    <?php $__env->startPush('after-script'); ?>
        <script src="<?php echo e(asset('assets')); ?>/js/app-invoice-print.js"></script>
    <?php $__env->stopPush(); ?>
    <?php $__env->startPush('script'); ?>
        <script>
            $(document).ready(function() {
                // Ambil tinggi dari elemen <pre>
                var preHeight = $('#notePre').outerHeight();
                // Atur tinggi elemen <p> menjadi sama dengan tinggi elemen <pre>
                $('#noteParagraph').css('height', preHeight + 'px');
            });
        </script>
    <?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/monitoring/client/hold-print.blade.php ENDPATH**/ ?>