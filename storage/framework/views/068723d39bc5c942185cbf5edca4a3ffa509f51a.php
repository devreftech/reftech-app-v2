<form action="<?php echo e(route('opname.store_product', $opname->id)); ?>" method="post" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>

    
    <div class="modal animate__animated animate__fadeIn" id="createProductOpname" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5"> Create Stock Opname
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
                        <div class="col-12 ">
                            <div class="form-floating form-floating-outline mb-2">
                                <select class="select2 form-select" data-allow-clear="true" name="replacement"
                                    id="replacement">
                                    <option> ---- Choose Replacement Here ---- </option>
                                    <?php $__currentLoopData = $product; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $replacement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($replacement->id); ?>">
                                            <?php echo e($replacement->replacement); ?> (
                                            <?php echo e(substr($replacement->product->go ?? 'N', 0, 1)); ?> )
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <label for="Replacement" class="mb-2">Replacement</label>
                            </div>
                        </div>
                        <div class="col-4 mb-2">
                            <div class="form-floating form-floating-outline">
                                <p class="mb-2 repeater-title">Stock Web</p>
                                <input type="number" class="form-control" placeholder="Pilih Dahulu Replacement..."
                                    name="stock_sistem" id="sistem" disabled value="">
                                <p class="text-muted">Pilih Dahulu Replacement</p>
                            </div>
                        </div>

                        <div class="col-2 mb-2">
                            <div class="form-floating form-floating-outline">
                                <p class="mb-2 repeater-title">Stock BDG</p>
                                <input type="number" class="form-control" placeholder="Pilih Dahulu Replacement..."
                                    name="stock_bdg" id="bdg" value="0">
                            </div>
                        </div>
                        <div class="col-2 mb-2">
                            <div class="form-floating form-floating-outline">
                                <p class="mb-2 repeater-title">Stock BKS</p>
                                <input type="number" class="form-control" placeholder="Pilih Dahulu Replacement..."
                                    name="stock_bks" id="bks" value="0">
                            </div>
                        </div>
                        <div class="col-4 mb-2">
                            <div class="form-floating form-floating-outline">
                                <p class="mb-2 repeater-title">selisih</p>
                                <input type="number" class="form-control" placeholder="Count Selisih Here..."
                                    name="stock_selisih" id="selisih" min='0' disabled value="">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating form-floating-outline">
                                <textarea class="form-control" name="note" id="note" placeholder="Text Note Here..."></textarea>
                                <label for="note">Note</label>
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
    </div>
</form>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/warehouse/opname/form-product.blade.php ENDPATH**/ ?>