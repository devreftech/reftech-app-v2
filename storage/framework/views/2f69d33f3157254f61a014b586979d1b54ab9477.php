<form action="<?php echo e(route('unit-sparepart.store', $product->id)); ?>" method="post" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <div class="modal animate__animated animate__fadeIn" id="createSparepart" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5">Create Sparepart of <?php echo e($product->sku); ?>

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
                    <div class="row g-2 mb-3 align-items-center">
                        <div class="col-md-6 col-12 mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="select2 form-select form-select-lg invoice-item-client"
                                    data-allow-clear="true" name="id_equivalent" id="selectclient">
                                    <option selected>----- Select Sparepart -----</option>
                                    <?php $__currentLoopData = $equivalent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($item->id); ?>">
                                            <?php if($item->product): ?>
                                                <?php echo e($item->brand); ?> <?php echo e($item->pn); ?> - <?php echo e($item->product->detail_desc); ?> ||
                                                <?php echo e($item->product->category); ?>

                                                (<?php echo e($item->product->go == 'Replacement' ? 'R' : 'G'); ?>)
                                            <?php else: ?>
                                                <?php echo e($item->pn); ?> - [Product Not Found]
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <label for="selectclient">Sparepart</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="number" class="form-control invoice-item-qty" placeholder="Min 1"
                                    name="qty" id="qty" min="1"
                                    value="<?php echo e(old('qty', @$sparepart->qty)); ?>">
                                <label for="qty">Quantity</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" name="pm_level" id="pm_level">
                                    <option value="PM1" <?php echo e(old('pm_level') == 'PM1' ? 'selected' : ''); ?>>PM1 (Minor)</option>
                                    <option value="PM2" <?php echo e(old('pm_level') == 'PM2' ? 'selected' : ''); ?>>PM2 (Major)</option>
                                    <option value="PM3" <?php echo e(old('pm_level') == 'PM3' ? 'selected' : ''); ?>>PM3</option>
                                    <option value="PM4" <?php echo e(old('pm_level') == 'PM4' ? 'selected' : ''); ?>>PM4</option>
                                </select>
                                <label for="pm_level">PM Level</label>
                            </div>
                        </div>
                    </div>
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
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/warehouse/unit/sparepart.blade.php ENDPATH**/ ?>