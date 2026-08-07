<form
    action="<?php echo e(route('purchase.add_pph', $purchase->id)); ?>"
    method="post" enctype="multipart/form-data">
    
    <?php echo csrf_field(); ?>
    <div class="modal animate__animated animate__fadeIn" id="addPph" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center" id="exampleModalLabel5">Add PPH
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
                    <?php $__currentLoopData = $dPurchase; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="row g-2 mb-3">
                            <div class="col-8">
                                <p class="fw-medium">
                                    <?php echo e($product->product); ?>

                                </p>
                            </div>
                            <div class="col-4">
                                <div class="input-group input-group-merge">
                                    <input type="number" class="form-control" placeholder="2" name="pph[]"
                                        aria-label="Amount (to the nearest dollar)" value="0">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/purchase/pph.blade.php ENDPATH**/ ?>