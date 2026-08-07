<form id="formEditStock" action="<?php echo e(route('opname.update_product', $opname->id)); ?>" class="link_route" method="post" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    
    <div class="modal animate__animated animate__fadeIn" id="updateStock" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title edit_title" id="exampleModalLabel5">
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
                            <div class="form-floating form-floating-outline">
                                <p class="mb-2 repeater-title">Stock Web</p>
                                <input type="number" class="form-control edit_sistem" placeholder="Pilih Dahulu Replacement..."
                                    name="stock_sistem" id="sistem" disabled value="">
                                <p class="text-muted">Pilih Dahulu Replacement</p>
                            </div>
                        </div>
                        
                        <div class="col-2 mb-2">
                            <div class="form-floating form-floating-outline">
                                <p class="mb-2 repeater-title">Stock BDG</p>
                                <input type="number" class="form-control edit_bdg" placeholder="Pilih Dahulu Replacement..."
                                    name="stock_bdg" value="0">
                            </div>
                        </div>
                        <div class="col-2 mb-2">
                            <div class="form-floating form-floating-outline">
                                <p class="mb-2 repeater-title">Stock BKS</p>
                                <input type="number" class="form-control edit_bks" placeholder="Pilih Dahulu Replacement..."
                                    name="stock_bks" value="0">
                            </div>
                        </div>
                        <div class="col-4 mb-2">
                            <div class="form-floating form-floating-outline">
                                <p class="mb-2 repeater-title">selisih</p>
                                <input type="number" class="form-control edit_selisih" placeholder="Count Selisih Here..."
                                    name="stock_selisih" id="selisih" min='0' disabled value="">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating form-floating-outline">
                                <textarea class="form-control edit_note" name="note" id="note" placeholder="Text Note Here..."></textarea>
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
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/warehouse/opname/edit-opname.blade.php ENDPATH**/ ?>