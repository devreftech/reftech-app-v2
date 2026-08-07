<form action="<?php echo e(route('stock.update', $product->id)); ?>" method="post" enctype="multipart/form-data">
    
    <?php echo csrf_field(); ?>

    <?php if(@$product): ?>
        <?php echo method_field('patch'); ?>
    <?php endif; ?>
    <div class="modal animate__animated animate__fadeIn" id="<?php echo e('updateStock-' . $product->id); ?>" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5-stock-<?php echo e($product->id); ?>">
                        <?php echo e('Update Stock' . @$product->commodity); ?>

                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <div class="row g-2 mb-3">
                        <div class="col-4 mb-2">
                            <p>First Stock</p>
                        </div>
                        <div class="col-8 mb-2">
                            <div class="row">
                                <div class="col-8">
                                    <div class="form-floating form-floating-outline">
                                        <input type="number" id="first_stock" class="form-control" name="first_stock"
                                            value="<?php echo e(old('first_stock', $product->first_stock)); ?>">
                                        <label for="first_stock">First Stock</label>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="date" id="Date" name="date"
                                            value="<?php echo e(old('date', $product->date ?? now()->format('Y-m-d'))); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-4 mb-2">
                            <p>Recent Stock</p>
                        </div>
                        <div class="col-8 mb-2">
                            <div class="form-floating form-floating-outline mb-3">
                                <input type="number" id="recent-office-stock-label" class="form-control recent-office-stock-label"
                                    name="stock" value="<?php echo e(old('stock', $product->stock)); ?>" disabled>
                                <input type="number" id="recent-office-stock" class="form-control recent-office-stock"
                                    name="office_recent_stock" value="<?php echo e(old('stock', $product->stock)); ?>" hidden>
                                <label for="recent-office-stock-label">Recent Office Stock</label>
                            </div>
                            <div class="form-floating form-floating-outline mb-3">
                                <input type="number" id="recent-warehouse-stock-label" class="form-control recent-warehouse-stock-label"
                                    name="stock" value="<?php echo e(old('stock', $product->warehouse_stock)); ?>" disabled>
                                <input type="number" id="recent-warehouse-stock" class="form-control recent-warehouse-stock"
                                    name="warehouse_recent_stock" value="<?php echo e(old('stock', $product->warehouse_stock)); ?>"
                                    hidden>
                                <label for="recent-warehouse-stock-label">Recent Warehouse Stock</label>
                            </div>
                            <div class="form-floating form-floating-outline">
                                <input type="number" id="pending_stock" class="form-control" name="pending_recent_stock"
                                    value="<?php echo e(old('pending_stock', $product->pending_stock)); ?>">
                                <label for="Recent Stock">Recent Pending Stock</label>
                            </div>
                        </div>
                    </div>
                    <?php
                        $i = 0;
                    ?>
                    <?php $__empty_1 = true; $__currentLoopData = $details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="row g-2 mb-3">
                            <div class="col-1"></div>
                            <div class="col-3 mb-2 d-flex align-item-center">
                                <p style="margin: auto"><?php echo e($detail->replacement); ?></p>
                            </div>
                            <div class="col-6 mb-2">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="number" id="office-stock-<?php echo e($i); ?>" class="form-control office-stock"
                                        name="office_stock[]" data-id="<?php echo e($i); ?>"
                                        value="<?php echo e(old('stock', $detail->stock)); ?>">
                                    <label for="office-stock-<?php echo e($i); ?>"> Office Stock</label>
                                </div>
                                <div class="form-floating form-floating-outline">
                                    <input type="number" id="warehouse-stock-<?php echo e($i); ?>" class="form-control warehouse-stock"
                                        name="warehouse_stock[]" data-id="<?php echo e($i); ?>"
                                        value="<?php echo e(old('stock', $detail->warehouse_stock)); ?>">
                                    <label for="warehouse-stock-<?php echo e($i); ?>"> Warehouse Stock</label>
                                </div>
                            </div>
                            <div class="col-2"></div>
                        </div>
                        <hr>
                        <?php
                            $i++;
                        ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p> Anda Belum memiliki Replacement. </p>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect"
                        data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</form>

<?php $__env->startPush('script'); ?>
    <script>
        $(() => {
            $('.office-stock').on('keyup change', function() {
                var total = 0;
                $('.office-stock').each(function() {
                    total += parseInt($(this).val());
                });
                $('.recent-office-stock-label').val(total);
                $('.recent-office-stock').val(total);
            });
            $('.warehouse-stock').on('keyup change', function() {
                var total = 0;
                $('.warehouse-stock').each(function() {
                    total += parseInt($(this).val());
                });
                $('.recent-warehouse-stock-label').val(total);
                $('.recent-warehouse-stock').val(total);
            });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/warehouse/product/stock.blade.php ENDPATH**/ ?>