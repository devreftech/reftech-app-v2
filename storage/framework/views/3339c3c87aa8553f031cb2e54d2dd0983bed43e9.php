<form action="<?php echo e(route('plant.crm.store', $existing->id)); ?>" method="post">
    <?php echo csrf_field(); ?>
    <div class="modal animate__animated animate__fadeIn" id="<?php echo e('createPlant'); ?>" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?php echo e('Tambah '); ?>Plant</h4>
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
                        <div class="col-12 mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="namePlantAnimation" class="form-control" name="namePlant"
                                    placeholder="example: Plant 2 - Cikarang" value="<?php echo e(old('namePlant')); ?>">
                                <label for="namePlantAnimation">Nama Plant</label>
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <div class="form-floating form-floating-outline">
                                <textarea id="addressPlantAnimation" class="form-control" name="addressPlant" style="height: 100px"
                                    placeholder="Alamat lengkap plant"><?php echo e(old('addressPlant')); ?></textarea>
                                <label for="addressPlantAnimation">Alamat Plant</label>
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
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/plant/form-create.blade.php ENDPATH**/ ?>