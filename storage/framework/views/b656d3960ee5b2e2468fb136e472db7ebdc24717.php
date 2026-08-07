<form action="<?php echo e(route('product.replacement.update', $detail->id)); ?>" method="POST" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PATCH'); ?>

    <div class="modal animate__animated animate__fadeIn" id="editReplacement-<?php echo e($detail->id); ?>" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5-<?php echo e($detail->id); ?>"> Edit Price Replacement <?php echo e($detail->replacement); ?>

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
                        <div class="col">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="replacement-<?php echo e($detail->id); ?>" class="form-control" name="replacement"
                                    placeholder="......" value="<?php echo e($detail->replacement); ?>">
                                <label for="replacement-<?php echo e($detail->id); ?>">Replacement</label>
                            </div>
                        </div>
                        <?php if(Auth::user()->role == 'Admin'): ?>
                            <div class="col mb-2">
                                <div class="col mb-2">
                                    <div class="input-group form-floating form-floating-outline" data-price="1">
                                        <span class="input-group-text">Rp. </span>
                                        <input type="text" class="form-control invoice-item-modal-label"
                                            id="modal-label-<?php echo e($detail->id); ?>" data-id="<?php echo e($detail->id); ?>" min="0"
                                            placeholder="Put modal Here" data-type="currency"
                                            pattern="^[0-9]\d{0,2}(\.\d{3})*$" @focus="focused = true"
                                            @blur="focused = false"
                                            value="<?php echo e(old('modal', @$detail->modal ? number_format($detail->modal, 0, ',', '.') : '')); ?>">
                                        <input class="form-control invoice-item-modal" type="number" name="modal"
                                            id="modal-<?php echo e($detail->id); ?>"
                                            value="<?php echo e(old('modal', @$detail->modal ?? '')); ?>" hidden>
                                    </div>
                                </div>
                            </div>
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
    </div>
</form>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/warehouse/replacement/form-price.blade.php ENDPATH**/ ?>