<form action="<?php echo e(route('invoice.expense', $invoice->id)); ?>" method="post" enctype="multipart/form-data">
    
    <?php echo csrf_field(); ?>
    <div class="modal modal-lg animate__animated animate__fadeIn" id="addExpense" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center" id="exampleModalLabel5">Add Expense <?php echo e($invoice->no_invoice); ?>

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
                            <div class="col-12 col-md-6 mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="descAnimation" class="form-control" name="desc"
                                        placeholder="Description Here" value="<?php echo e(old('desc')); ?>">
                                    <label for="descAnimation">Description</label>
                                </div>
                            </div>
                            <div class="col-4 col-md-1 mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" id="descAnimation" class="form-control" name="qty"
                                        placeholder="Description Here" value="<?php echo e(old('desc')); ?>" min="1">
                                    <label for="descAnimation">Qty</label>
                                </div>
                            </div>
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control invoice-item-price-label"
                                        id="priceLabel" data-id="1" name="harga"
                                        placeholder="Put Price Here" data-type="currency" min="0"
                                        pattern="^[0-9]\d{0,2}(\.\d{3})*$" @focus="focused = true"
                                        @blur="focused = false" value="<?php echo e(old('price')); ?>">
                                    <input class="form-control invoice-item-price" type="number"
                                        name="price" id="pricy" value="<?php echo e(old('price')); ?>" hidden>
                                    <label for="descAnimation">Price</label>
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
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/invoice/expense.blade.php ENDPATH**/ ?>